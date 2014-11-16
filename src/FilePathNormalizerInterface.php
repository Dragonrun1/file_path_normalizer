<?php
/**
 * Contains FilePathNormalizerInterface Interface.
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
 * Interface FilePathNormalizerInterface
 */
interface FilePathNormalizerInterface
{
    /**
     * @param string $file
     * @param bool   $absoluteRequired
     *
     * @throws DomainException
     * @throws InvalidArgumentException
     * @return string
     */
    public function normalizeFile($file, $absoluteRequired = true);
    /**
     * Used to normalize a file path without all the shortcomings of the
     * built-in functions.
     *
     * This should NOT be used with a string that includes the file name.
     *
     * @param string $path
     * @param bool   $absoluteRequired
     *
     * @see  FilePathNormalizerInterface::normalizeFile() Use to normalize full
     *                                                    path with a file name.
     * @throws DomainException
     * @throws InvalidArgumentException
     * @return string
     */
    public function normalizePath($path, $absoluteRequired = true);
}
