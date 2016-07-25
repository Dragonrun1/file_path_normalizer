<?php
declare(strict_types = 1);
/**
 * Contains PathInfoTraitSpec class.
 *
 * PHP version 7.0
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2016 Michael Cummings
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
 * @copyright 2016 Michael Cummings
 * @license   GPL-2.0
 */
/**
 * Test namespace.
 */
namespace Spec\FilePathNormalizer;

use FilePathNormalizer\PathInfoInterface;
use PhpSpec\ObjectBehavior;

/**
 * Class PathInfoTraitSpec
 *
 * @mixin \FilePathNormalizer\PathInfoTrait
 *
 * @method void during(string $method, array $params)
 * @method void shouldBe($value)
 * @method void shouldContain($value)
 * @method void shouldHaveKey($key)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 *
 * @since 2.0.0-dev New path info awareness.
 */
class PathInfoTraitSpec extends ObjectBehavior
{
    /**
     * @param \FilePathNormalizer\PathInfo $pathInfo
     */
    public function it_provides_fluent_interface_from_set_pathInfo($pathInfo)
    {
        /**
         * @var PathInfoInterface $pathInfo
         */
        $this->setPathInfo($pathInfo)
             ->shouldReturn($this);
    }
    public function it_should_return_new_instance_like_factory_first_call()
    {
        $result = $this->getPathInfo()
                       ->shouldHaveType('\FilePathNormalizer\PathInfoInterface');
        $this->getPathInfo()
             ->shouldReturn($result);
    }
    /**
     * @param \FilePathNormalizer\PathInfo $pathInfo
     */
    public function it_should_return_same_instance_that_it_is_given($pathInfo)
    {
        /**
         * @var PathInfoInterface $pathInfo
         */
        $this->setPathInfo($pathInfo)
             ->getPathInfo()
             ->shouldReturn($pathInfo);
    }
    /**
     * @internal PathInfoInterface $pathInfo
     */
    public function let(/**PathInfoInterface $pathInfo*/)
    {
        $this->beAnInstanceOf('\\Spec\\FilePathNormalizer\\MockPathInfoTrait');
    }
}
