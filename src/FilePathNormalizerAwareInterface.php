<?php
declare(strict_types=1);
/**
 * Contains interface FilePathNormalizerAwareInterface.
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
 * Interface FilePathNormalizerAwareInterface.
 *
 * @since 2.0.0-dev FPN Awareness.
 */
interface FilePathNormalizerAwareInterface
{
    /**
     * Get the instance of FilePathNormalizerInterface.
     *
     * Act like one time factory if property has not already been set.
     *
     * @return FilePathNormalizerInterface Return the instance.
     * @since 2.0.0-dev Added for completeness.
     * @api
     */
    public function getFpn(): FilePathNormalizerInterface;
    /**
     * Set the instance of FilePathNormalizerInterface.
     *
     * @param FilePathNormalizerInterface $value Instance to use.
     *
     * @return FilePathNormalizerAwareInterface
     * @since 2.0.0-dev Added for completeness.
     * @api
     */
    public function setFpn(FilePathNormalizerInterface $value = null): self;
}
