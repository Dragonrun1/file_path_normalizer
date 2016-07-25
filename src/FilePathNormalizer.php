<?php
declare(strict_types = 1);
/**
 * Contains FilePathNormalizer class.
 *
 * PHP version 7.0
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2014-2016 Michael Cummings
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the
 * Free Software Foundation, version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program. If not, see
 * <http://www.gnu.org/licenses/>.
 *
 * You should be able to find a copy of this license in the LICENSE file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2014-2016 Michael Cummings
 * @license   GPL-2.0
 */
/**
 * Main namespace.
 */
namespace FilePathNormalizer;

/**
 * Class FilePathNormalizer
 *
 * @since 0.1.0-dev The heart of the project.
 */
class FilePathNormalizer implements FilePathNormalizerInterface, PathInfoAwareInterface
{
    use PathInfoTrait;
    /**
     * FilePathNormalizer constructor.
     *
     * @param PathInfoInterface|null $pathInfo
     */
    public function __construct(PathInfoInterface $pathInfo = null)
    {
        $this->pathInfo = $pathInfo;
    }
    /**
     * Used to normalize a file with a path.
     *
     * Note MUST include an actual file name and NOT just a path. Use
     * normalizePath() for checking paths without file names.
     *
     * @param string $file      File with a path.
     * @param int    $options   Determines the options FPN uses while
     *                          validating path.
     *
     * @return string Returns the file name with a normalized path.
     * @throws \DomainException
     * @throws \LogicException
     * @since 0.2.0-dev Added to making using class easier.
     * @uses  FilePathNormalizer::normalizePath() to normalize path part.
     * @api
     */
    public function normalizeFile(string $file, int $options = self::MODE_DEFAULT): string
    {
        list($fileName, $path) = explode('/', strrev(str_replace('\\', '/', $file)), 2);
        $path = strrev($path);
        $fileName = trim(strrev($fileName));
        if ('' === $fileName) {
            $mess = 'An empty file name is NOT allowed';
            throw new \DomainException($mess);
        }
        if (!ctype_print($fileName)) {
            $mess = 'Using any non-printable characters in the file name is NOT allowed';
            throw new \DomainException($mess);
        }
        return $this->normalizePath($path, $options) . $fileName;
    }
    /**
     * Used to normalize a file path without all the shortcomings of the
     * built-in functions.
     *
     * This should NOT be used with a string that includes a file name, instead
     * use NormalizeFile() when wanting to check both.
     *
     * Note that path length is not checked since the OS, character set used,
     * and/or filesystem being used can change what is allowed.
     *
     * @param string $path      Path to be normalized.
     * @param int    $options   Determines the options FPN uses while
     *                          validating path.
     *
     * @see   FilePathNormalizerInterface::normalizeFile() Use to normalize full
     *                                                    path with a file name.
     * @return string Returns the normalized path.
     * @throws \DomainException
     * @since 0.2.0-dev Added to making using class easier.
     * @api
     */
    public function normalizePath(string $path, int $options = self::MODE_DEFAULT): string
    {
        $this->validateOptions($options);
        $pathInfo = $this->getPathInfo();
        $pathInfo->initAll($path);
        $this->checkWrappers($options);
        $this->absoluteChecks($options);
        $path = '';
        if ($pathInfo->hasWrappers()) {
            $path .= implode('://', $pathInfo->getWrapperList()) . '://';
        }
        $path .= $pathInfo->getRoot() . implode('/', $this->cleanPartsPath()) . '/';
        return $path;
    }
    /**
     * @param int    $options
     *
     * @throws \DomainException
     */
    protected function absoluteChecks(int $options)
    {
        if (($options & self::ABSOLUTE_DISABLED) && $this->getPathInfo()
                                                         ->isAbsolutePath()
        ) {
            $mess = 'Given absolute path when absolute was disabled';
            throw new \DomainException($mess);
        }
        if (($options & self::ABSOLUTE_REQUIRED) && !$this->getPathInfo()
                                                          ->isAbsolutePath()
        ) {
            $mess = 'Absolute path required but root part missing';
            throw new \DomainException($mess);
        }
    }
    /**
     * Use to make check on the wrapper part of path.
     *
     * @param int $options Options use in checks.
     *
     * @throws \DomainException
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    protected function checkWrappers(int $options)
    {
        $hasWrappers = $this->getPathInfo()
                            ->hasWrappers();
        if (($options & self::WRAPPER_DISABLED) && $hasWrappers) {
            $mess = 'Given wrapper when wrapper(s) are disabled';
            throw new \DomainException($mess);
        }
        if (($options & self::WRAPPER_REQUIRED) && !$hasWrappers) {
            $mess = 'Missing wrapper when wrapper(s) are required';
            throw new \DomainException($mess);
        }
        $wrappers = $this->getPathInfo()
                         ->getWrapperList();
        $hasVfs = in_array('vfs', $wrappers, true);
        if (($options & self::VFS_DISABLED) && $hasVfs) {
            $mess = 'Found vfsStream wrapper when it was disabled';
            throw new \DomainException($mess);
        }
        if (($options & self::VFS_REQUIRED) && !$hasVfs) {
            $mess = 'Missing vfsStream wrapper when it was required';
            throw new \DomainException($mess);
        }
        /** @noinspection LowPerformanceArrayUniqueUsageInspection */
        /*
         * Though technically allowed duplicate wrappers are most likely to be
         * user errors, programming bugs, or at least a bad programming smell.
         */
        if (count($wrappers) !== count(array_unique($wrappers))) {
            $mess = 'Duplicate wrappers are not allowed';
            throw new \DomainException($mess);
        }
        $regExp = '%^[[:alpha:]][[:alnum:]]+$%';
        if (false === array_reduce($wrappers, function ($carry, $item) use ($regExp) {
                return $carry && preg_match($regExp, $item);
            }, true)
        ) {
            $mess = 'Invalidly formatted wrapper name found';
            throw new \DomainException($mess);
        }
        $last = array_pop($wrappers);
        if ($hasVfs && 'vfs' !== $last) {
            $mess = 'Must use vfsStream as last wrapper';
            throw new \DomainException($mess);
        }
    }
    /**
     * Cleans up the actual path part of the given string.
     *
     * @return string[]
     * @throws \DomainException
     * @since 0.1.0-dev The heart of the project.
     */
    protected function cleanPartsPath(): array
    {
        $parts = [];
        foreach ($this->getPathInfo()
                      ->getDirList() as $part) {
            if ('.' === $part) {
                continue;
            }
            if ('..' === $part) {
                /*
                 * Though path may still be valid in the file system in the
                 * case of relative paths here there is no way to validate
                 * them without accessing the file system to do so. These are
                 * unusual paths probably cause by errors or to access
                 * something unexpected anyway.
                 */
                if (count($parts) < 1) {
                    $mess = 'Unusual above root path found';
                    throw new \DomainException($mess);
                }
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        return $parts;
    }
    /**
     * @param int $options
     *
     * @throws \DomainException
     */
    protected function validateOptions(int $options)
    {
        // Forbidden combos.
        $combos = [
            self::ABSOLUTE_DISABLED | self::ABSOLUTE_ALLOWED,
            self::ABSOLUTE_DISABLED | self::ABSOLUTE_REQUIRED,
            self::VFS_ALLOWED | self::ABSOLUTE_ALLOWED,
            self::VFS_ALLOWED | self::ABSOLUTE_REQUIRED,
            self::VFS_DISABLED | self::VFS_ALLOWED,
            self::VFS_DISABLED | self::VFS_REQUIRED,
            self::VFS_REQUIRED | self::ABSOLUTE_ALLOWED,
            self::VFS_REQUIRED | self::ABSOLUTE_REQUIRED,
            self::WRAPPER_DISABLED | self::VFS_ALLOWED,
            self::WRAPPER_DISABLED | self::VFS_REQUIRED,
            self::WRAPPER_DISABLED | self::WRAPPER_ALLOWED,
            self::WRAPPER_DISABLED | self::WRAPPER_REQUIRED
        ];
        /** @noinspection ForeachSourceInspection */
        foreach ($combos as $combo) {
            if (($options & $combo) === $combo) {
                $mess = 'Can not use required or allowed options together with corresponding disabled option';
                throw new \DomainException($mess);
            }
        }
        // Pointless combos.
    }
}
