<?php
declare(strict_types=1);
/**
 * Contains interface PathInfoAwareInterface.
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
/**
 * Main namespace.
 */

namespace FilePathNormalizer;

/**
 * Interface PathInfoAwareInterface
 *
 * @since 2.0.0-dev New path info awareness.
 */
interface PathInfoAwareInterface
{
    /**
     * @return PathInfoInterface
     * @throws \LogicException
     * @api
     */
    public function getPathInfo(): PathInfoInterface;
    /**
     * @param PathInfoInterface $value
     *
     * @return PathInfoAwareInterface Fluent interface
     * @api
     */
    public function setPathInfo(?PathInfoInterface $value = null): PathInfoAwareInterface;
}
