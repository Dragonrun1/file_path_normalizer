<?php
declare(strict_types=1);
/**
 * Contains trait PathInfoTrait.
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
 * Trait PathInfoTrait.
 *
 * @since 2.0.0-dev New path info awareness.
 */
trait PathInfoTrait
{
    /**
     * @return PathInfoInterface
     * @throws \DomainException
     * @api
     */
    public function getPathInfo(): PathInfoInterface
    {
        if (null === $this->pathInfo) {
            $this->pathInfo = new PathInfo('.');
        }
        return $this->pathInfo;
    }
    /**
     * @param PathInfoInterface $value
     *
     * @return PathInfoAwareInterface|PathInfoTrait Fluent interface.
     * @api
     */
    public function setPathInfo(?PathInfoInterface $value = null): PathInfoAwareInterface
    {
        $this->pathInfo = $value;
        return $this;
    }
    /**
     * @var PathInfoInterface pathInfo
     */
    private $pathInfo;
}
