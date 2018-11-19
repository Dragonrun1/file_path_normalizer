<?php
declare(strict_types=1);
/**
 * Contains FilePathNormalizerInterface Interface.
 *
 * PHP version 7.1
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
 * Interface FilePathNormalizerInterface
 *
 * @since 0.2.0-dev Added to making using class easier.
 */
interface FilePathNormalizerInterface
{
    /*
     * These constants are used for the new $options parameter that has replaced
     * the old $absoluteRequired one.
     */
    /**
     * Path can be absolute or relative.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const ABSOLUTE_ALLOWED = 1;
    /**
     * Allows only relative path.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const ABSOLUTE_DISABLED = 2;
    /**
     * Absolute path required.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const ABSOLUTE_REQUIRED = 4;
    /**
     * Sets mode that is backwards compatible with earlier versions of File Path Normalizer.
     *
     * Absolute path required, Virtual Filesystem Stream allowed, wrapper allowed.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     * @since 2.0.0-dev Absolute required and Virtual Filesystem Stream allowed no longer compatible together.
     *        VFS_DISABLED now used.
     */
    public const MODE_DEFAULT = self::ABSOLUTE_REQUIRED | self::VFS_DISABLED | self::WRAPPER_ALLOWED;
    /**
     * Allows use of VFSStream wrapper.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const VFS_ALLOWED = 8;
    /**
     * Disables use of VFSStream.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const VFS_DISABLED = 16;
    /**
     * Require VFSStream in wrappers.
     *
     * @since 2.0.0-dev Added as might be useful for testing.
     */
    public const VFS_REQUIRED = 32;
    /**
     * Allows path to have optional wrappers.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const WRAPPER_ALLOWED = 64;
    /**
     * Disables path from have any wrappers.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const WRAPPER_DISABLED = 128;
    /**
     * Makes use of one or more wrapper required.
     *
     * @since 1.1.0-dev Absolute required to options conversion.
     */
    public const WRAPPER_REQUIRED = 256;
    /**
     * Used to normalize a file with a path.
     *
     * @param string $file      File with a path.
     * @param int    $options   Determines the options FPN uses while
     *                          validating path.
     *
     * @return string Returns the file with a normalized path.
     * @since 0.2.0-dev Added to making using class easier.
     * @api
     */
    public function normalizeFile(string $file, int $options = self::MODE_DEFAULT): string;
    /**
     * Used to normalize a file path without all the shortcomings of the
     * built-in functions.
     *
     * This should NOT be used with a string that includes the file name.
     *
     * @param string $path      Path to be normalized.
     * @param int    $options   Determines the options FPN uses while
     *                          validating path.
     *
     * @see   FilePathNormalizerInterface::normalizeFile() Use to normalize full
     *                                                    path with a file name.
     * @return string Returns the normalized path.
     * @since 0.2.0-dev Added to making using class easier.
     * @api
     */
    public function normalizePath(string $path, int $options = self::MODE_DEFAULT): string;
}
