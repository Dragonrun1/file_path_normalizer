<?php
/**
 * Contains FilePathNormalizerTraitSpec class.
 *
 * PHP version 5.4
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2015 Michael Cummings
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
 * @copyright 2015 Michael Cummings
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU GPLv2
 * @author    Michael Cummings <mgcummings@yahoo.com>
 */

namespace Spec\FilePathNormalizer;

use PhpSpec\ObjectBehavior;

/**
 * Class FilePathNormalizerTraitSpec
 *
 * @mixin \FilePathNormalizer\FilePathNormalizerTrait
 *
 * @method void shouldReturn()
 */
class FilePathNormalizerTraitSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beAnInstanceOf('\\Spec\\FilePathNormalizer\\MockFilePathNormalizerTrait');
    }
    /**
     * @param \FilePathNormalizer\FilePathNormalizer $fpn Instance of fpn.
     */
    public function itProvidesFluentInterfaceFromSetFpn($fpn)
    {
        $this->setFpn($fpn)->shouldReturn($this);
    }
}
