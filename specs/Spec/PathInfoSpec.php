<?php
declare(strict_types = 1);
/**
 * Contains class PathInfoSpec.
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

use PhpSpec\ObjectBehavior;

/**
 * Class PathInfoSpec
 *
 * @since 2.0.0-dev New path info awareness.
 */
class PathInfoSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('FilePathNormalizer\PathInfo');
    }
    public function it_throws_exception_for_empty_path_from_initAll()
    {
        $paths = [
            '',
            ' ',
            '  '
        ];
        $mess = 'An empty path is NOT allowed';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('initAll', [$path]);
        }
    }
    public function it_throws_exception_for_illegal_characters_in_path_from_initAll()
    {
        $paths = [
            "\034",
            " \034",
            "\034  "
        ];
        $mess = 'Using any non-printable characters in the path is NOT allowed';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('initAll', [$path]);
        }
    }
    public function let()
    {
        $this->beConstructedWith('dummy');
    }
}
