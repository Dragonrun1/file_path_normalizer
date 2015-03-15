<?php
/**
 * Contains FilePathNormalizerTrait Trait.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2014-2015 Michael Cummings
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
 * @copyright 2014-2015 Michael Cummings
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU GPLv2
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
/**
 * Main namespace.
 */
namespace FilePathNormalizer;

/**
 * Trait FilePathNormalizerTrait
 *
 * @since 0.2.0 Added to making using class easier.
 */
trait FilePathNormalizerTrait
{
    /**
     * Set the instance of FilePathNormalizerInterface.
     *
     * @param FilePathNormalizerInterface|null $value Instance to use.
     *
     * @return $this Fluent interface.
     * @since 0.2.0 Added to making using class easier.
     * @api
     */
    public function setFpn(FilePathNormalizerInterface $value = null)
    {
        $this->fpn = $value;
        return $this;
    }
    /**
     * Get the instance of FilePathNormalizerInterface.
     *
     * @return FilePathNormalizerInterface Return the instance.
     * @since 0.2.0 Added to making using class easier.
     * @api
     */
    protected function getFpn()
    {
        if (null === $this->fpn) {
            $this->fpn = new FilePathNormalizer();
        }
        return $this->fpn;
    }
    /**
     * Holds the instance of FilePathNormalizerInterface.
     *
     * @type FilePathNormalizerInterface $fpn
     */
    protected $fpn;
}
