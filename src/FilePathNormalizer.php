<?php
/**
 * Contains FilePathNormalizer class.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2014 Michael Cummings
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
 * @copyright 2014 Michael Cummings
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU GPLv2
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
namespace FilePathNormalizer;

/**
 * Class FilePathNormalizer
 */
class FilePathNormalizer implements FilePathNormalizerInterface
{
    /**
     * @inheritdoc
     *
     * @uses FilePathNormalizer::normalizePath()
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    public function normalizeFile($file, $options = self::MODE_DEFAULT)
    {
        if (!is_string($file)) {
            $mess = 'String expected but was given ' . gettype($file);
            throw new \InvalidArgumentException($mess);
        }
        list($fileName, $path) = explode(
            '/',
            strrev(str_replace('\\', '/', $file)),
            2
        );
        if (!ctype_print($fileName)) {
            $mess = 'File name can NOT have non-printable characters or be empty';
            throw new \DomainException($mess);
        }
        $this->checkPathParameter($path);
        return $this->normalizePath(
            strrev($path),
            $options
        ) . strrev($fileName);
    }
    /**
     * @inheritdoc
     *
     * @uses FilePathNormalizer::cleanPartsPath()
     * @throws \DomainException
     * @throws \InvalidArgumentException
     */
    public function normalizePath($path, $options = self::MODE_DEFAULT)
    {
        $this->checkPathParameter($path);
        $path = trim($path);
        if (is_bool($options)) {
            $options = $options ? self::MODE_DEFAULT :
                self::ABSOLUTE_ALLOWED
                | self::VFS_ALLOWED
                | self::WRAPPER_ALLOWED;
        } elseif (!is_int($options)) {
            $mess = sprintf('Options MUST be boolean or integer, but was given %s', gettype($options));
            throw new \DomainException($mess);
        }
        $path = str_replace('\\', '/', $path);
        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';
        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';
        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';
        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = 'Path is NOT valid, was given ' . $path;
            throw new \DomainException($mess);
        }
        $wrappers = $parts['wrappers'];
        $this->wrapperChecks($wrappers, $options);
        // vfsStream does NOT allow absolute path.
        if ('vfs://' === substr($wrappers, -6)) {
            $options &= ~self::ABSOLUTE_REQUIRED;
        }
        if (($options & self::ABSOLUTE_REQUIRED) && empty($parts['root'])) {
            $mess = sprintf(
                'Absolute path required but root part missing, was given %s',
                $path
            );
            throw new \DomainException($mess);
        }
        $root = $parts['root'];
        $parts = $this->cleanPartsPath($parts['path']);
        $path = $wrappers . $root . implode('/', $parts);
        if ('/' !== substr($path, -1)) {
            $path .= '/';
        }
        return $path;
    }
    /**
     * Checks if given path is valid.
     *
     * @param string $path Path to be checked.
     */
    protected function checkPathParameter($path)
    {
        if (!is_string($path)) {
            $mess = 'String expected but was given ' . gettype($path);
            throw new \InvalidArgumentException($mess);
        }
        if (!ctype_print($path)) {
            $mess = 'Path can NOT have non-printable characters or be empty';
            throw new \DomainException($mess);
        }
    }
    /**
     * @param string $path
     *
     * @throws \DomainException
     * @return string[]
     */
    protected function cleanPartsPath($path)
    {
        // Drop all leading and trailing "/"s.
        $path = trim($path, '/');
        // Drop pointless consecutive "/"s.
        while (false !== strpos($path, '//')) {
            $path = str_replace('//', '/', $path);
        }
        $parts = [];
        foreach (explode('/', $path) as $part) {
            if ('.' === $part || '' === $part) {
                continue;
            }
            if ('..' === $part) {
                if (count($parts) < 1) {
                    $mess = 'Can NOT go above root path but was given ' . $path;
                    throw new \DomainException($mess, 1);
                }
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        return $parts;
    }
    /**
     * Use to make check on the wrapper part of path.
     *
     * @param string $wrapper Wrapper to be checked.
     * @param int $options Options use in checks.
     */
    protected function wrapperChecks($wrapper, $options)
    {
        if ('' === $wrapper && ($options & self::WRAPPER_REQUIRED)) {
            $mess = 'Missing wrapper(s) when required set';
            throw new \DomainException($mess);
        }
        if ('' !== $wrapper && ($options & self::WRAPPER_DISABLED)) {
            $mess = 'Given wrapper(s) when wrapper disabled';
            throw new \DomainException($mess);
        }
        $wrappers = explode('://', $wrapper);
        array_pop($wrappers);
        $regExp = '%^[[:alpha:]][[:alnum:]]+$%';
        $func = function ($carry, $item) use ($regExp) {
            return $carry && preg_match($regExp, $item);
        };
        if (false === array_reduce($wrappers, $func, true)) {
            $mess = sprintf('Invalid wrapper(s), was given %s', $wrapper);
            throw new \DomainException($mess);
        }
    }
}
