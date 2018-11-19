<?php
declare(strict_types=1);
/**
 * Contains interface PathInfoInterface.
 *
 * PHP version 7.1
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2016-2018 Michael Cummings
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
 * <http://spdx.org/licenses/GPL-2.0.html>.
 *
 * You should be able to find a copy of this license in the LICENSE file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2016-2018 Michael Cummings
 * @license   GPL-2.0
 */

namespace FilePathNormalizer;

/**
 * Class PathInfo.
 *
 * @since 2.0.0-dev New path info awareness.
 */
interface PathInfoInterface
{
    /**
     * Provides a lightly cleaned up array of the directory path parts without wrappers or root.
     *
     * The light cleaning done is to remove any leading or trailing whitespace chars,
     * remove any leading or trailing directory separators to prevent empty parts,
     * next remove any consecutive directory separators to prevent the empty
     * parts they would create,
     * and finally explode the path into an array of parts to be returned.
     *
     * @return array
     */
    public function getDirList(): array;
    /**
     * @return string
     */
    public function getDirs(): string;
    /**
     * @return string
     */
    public function getPath(): string;
    /**
     * @return string
     */
    public function getRoot(): string;
    /**
     * @return array
     */
    public function getWrapperList(): array;
    /**
     * @return string
     */
    public function getWrappers(): string;
    /**
     * @return bool
     */
    public function hasDirs(): bool;
    /**
     * @return bool
     */
    public function hasWrappers(): bool;
    /**
     * @param string $path
     *
     * @return $this Fluent interface.
     */
    public function initAll(string $path): self;
    /**
     * @return bool
     */
    public function isAbsolutePath(): bool;
}
