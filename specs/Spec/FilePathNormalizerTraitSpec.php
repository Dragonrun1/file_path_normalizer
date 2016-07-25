<?php
declare(strict_types = 1);
/**
 * Contains FilePathNormalizerTraitSpec class.
 *
 * PHP version 7.0
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2015-2016 Michael Cummings
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
 * @copyright 2015-2016 Michael Cummings
 * @license   GPL-2.0
 */
/**
 * Test namespace.
 */
namespace Spec\FilePathNormalizer;

use FilePathNormalizer\FilePathNormalizerInterface;
use PhpSpec\ObjectBehavior;

/**
 * Class FilePathNormalizerTraitSpec
 *
 * @mixin \FilePathNormalizer\FilePathNormalizerTrait
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldHaveKey($key)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class FilePathNormalizerTraitSpec extends ObjectBehavior
{
    /**
     * @param \FilePathNormalizer\FilePathNormalizer $fpn
     */
    public function it_provides_fluent_interface_from_set_fpn($fpn)
    {
        /**
         * @var FilePathNormalizerInterface $fpn
         */
        $this->setFpn($fpn)
             ->shouldReturn($this);
    }
    public function it_should_return_new_instance_like_factory_first_call()
    {
        $result = $this->getFpn()
                       ->shouldHaveType('\FilePathNormalizer\FilePathNormalizerInterface');
        $this->getFpn()
             ->shouldReturn($result);
    }
    /**
     * @param \FilePathNormalizer\FilePathNormalizer $fpn Instance of fpn.
     */
    public function it_should_return_same_instance_that_it_is_given($fpn)
    {
        $this->setFpn($fpn)
             ->getFpn()
             ->shouldReturn($fpn);
    }
    /**
     * @internal FilePathNormalizerInterface $fpn
     */
    public function let(/**FilePathNormalizerInterface $fpn*/)
    {
        $this->beAnInstanceOf('\\Spec\\FilePathNormalizer\\MockFilePathNormalizerTrait');
//        $this->willImplement('\\FilePathNormalizer\\FilePathNormalizerAwareInterface');
    }
}
