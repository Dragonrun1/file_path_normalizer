<?php
declare(strict_types = 1);
/**
 * Contains FilePathNormalizerTrait Trait.
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
 * @copyright 2014-2016 Michael Cummings
 * @license   GPL-2.0
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */
/**
 * Main namespace.
 */
namespace FilePathNormalizer;

/**
 * Trait FilePathNormalizerTrait
 *
 * @since 0.2.0-dev Added to making using class easier.
 */
trait FilePathNormalizerTrait
{
    /**
     * Get the instance of FilePathNormalizerInterface.
     *
     * Act like one time factory if property has not already been set.
     *
     * @return FilePathNormalizerInterface Return the instance.
     * @since 0.2.0-dev Added to making using class easier.
     * @api
     */
    public function getFpn(): FilePathNormalizerInterface
    {
        if (null === $this->fpn) {
            $this->fpn = new FilePathNormalizer();
        }
        return $this->fpn;
    }
    /**
     * Set the instance of FilePathNormalizerInterface.
     *
     * @param FilePathNormalizerInterface $value Instance to use.
     *
     * @return self
     * @since 0.2.0-dev Added to making using class easier.
     * @api
     */
    public function setFpn(FilePathNormalizerInterface $value): self
    {
        $this->fpn = $value;
        return $this;
    }
    /**
     * @type FilePathNormalizerInterface $fpn Holds the instance of FilePathNormalizerInterface.
     */
    private $fpn;
}
