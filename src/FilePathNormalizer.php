<?php
/**
 * Contains FilePathNormalizer class.
 *
 * PHP version 5.3
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

use DomainException;
use InvalidArgumentException;

/**
 * Class FilePathNormalizer
 */
class FilePathNormalizer implements FilePathNormalizerInterface
{
    /**
     * @param string $file
     * @param bool   $absoluteRequired
     *
     * @uses FilePathNormalizer::normalizePath()
     * @throws DomainException
     * @throws InvalidArgumentException
     * @return string
     */
    public function normalizeFile($file, $absoluteRequired = true)
    {
        if (!is_string($file)) {
            $mess = 'String expected but was given ' . gettype($file);
            throw new InvalidArgumentException($mess);
        }
        list($fileName, $path) = explode('/',
            strrev(str_replace('\\', '/', $file)), 2);
        if (empty($fileName)) {
            $mess = 'File name can NOT be empty but was given ' . $file;
            throw new DomainException($mess);
        }
        return $this->normalizePath(strrev($path),
            $absoluteRequired) . strrev($fileName);
    }
    /**
     * Used to normalize a file path without all the short comings of the
     * built-in functions.
     *
     * This should NOT be used with a string that includes the file name.
     *
     * @param string $path
     * @param bool   $absoluteRequired
     *
     * @see  FilePathNormalizer::normalizeFile() Use to normalize full path with
     *                                          a file name.
     * @uses FilePathNormalizer::cleanPartsPath()
     * @throws DomainException
     * @throws InvalidArgumentException
     * @return string
     */
    public function normalizePath($path, $absoluteRequired = true)
    {
        if (!is_string($path)) {
            $mess = 'String expected but was given ' . gettype($path);
            throw new InvalidArgumentException($mess);
        }
        $path = str_replace('\\', '/', $path);
        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:alpha:]][[:alnum:]]+://)*)';
        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';
        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';
        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = 'Path is NOT valid, was given ' . $path;
            throw new DomainException($mess);
        }
        $wrappers = $parts['wrappers'];
        // vfsStream does NOT allow absolute path.
        if ('vfs://' == substr($wrappers, -6)) {
            $absoluteRequired = false;
        }
        if ($absoluteRequired && empty($parts['root'])) {
            $mess =
                'Absolute path required but was missing root part, was given '
                . $path;
            throw new DomainException($mess);
        }
        $root = $parts['root'];
        $parts = $this->cleanPartsPath($parts['path']);
        $path = $wrappers . $root . implode('/', $parts);
        if ('/' != substr($path, -1)) {
            $path .= '/';
        }
        return $path;
    }
    /**
     * @param string $path
     *
     * @throws DomainException
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
            if ('.' == $part || '' == $part) {
                continue;
            }
            if ('..' == $part) {
                if (count($parts) < 1) {
                    $mess = 'Can NOT go above root path but was given ' . $path;
                    throw new DomainException($mess, 1);
                }
                array_pop($parts);
                continue;
            }
            $parts[] = $part;
        }
        return $parts;
    }
}
