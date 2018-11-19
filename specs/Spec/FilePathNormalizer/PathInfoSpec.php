<?php
declare(strict_types=1);
/**
 * Contains class PathInfoSpec.
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
 * <http://www.gnu.org/licenses/>.
 *
 * You should be able to find a copy of this license in the LICENSE file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2016-2018 Michael Cummings
 * @license   GPL-2.0
 */
/**
 * Test namespace.
 */

namespace Spec\FilePathNormalizer;

use FilePathNormalizer\PathInfo;
use PhpSpec\ObjectBehavior;

/**
 * Class PathInfoSpec
 *
 * @mixin \FilePathNormalizer\PathInfo
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
class PathInfoSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PathInfo::class);
    }
    public function it_should_have_basic_getters_that_return_raw_parts(): void
    {
        $this->initAll('ftp:///dummy');
        $this->getWrappers()
             ->shouldReturn('ftp://');
        $this->getRoot()
             ->shouldReturn('/');
        $this->getDirs()
             ->shouldReturn('dummy');
    }
    public function it_should_return_correct_wrappers_from_get_wrappers_list(): void
    {
        $paths = [
            '/' => [],
            'c:/' => [],
            'dummy' => [],
            'ftp:///dummy/path' => ['ftp'],
            'ftp://vfs://c:\\dummy\\path\\' => ['ftp', 'vfs'],
            'ftp://vfs://dummy//path' => ['ftp', 'vfs']
        ];
        foreach ($paths as $path => $expected) {
            $this->initAll($path)
                 ->getWrapperList()
                 ->shouldReturn($expected);
        }
    }
    public function it_should_return_correctly_cleaned_dir_parts_from_get_dir_list(): void
    {
        $paths = [
            '/' => [],
            'c:/' => [],
            'dummy' => ['dummy'],
            '/dummy/path' => ['dummy', 'path'],
            'ftp://c:\\dummy\\path\\' => ['dummy', 'path'],
            'dummy//path' => ['dummy', 'path']
        ];
        foreach ($paths as $path => $expected) {
            $this->initAll($path)
                 ->getDirList()
                 ->shouldReturn($expected);
        }
    }
    public function it_should_return_original_given_path_from_get_path(): void
    {
        $this->initAll('dummy/')
             ->getPath()
             ->shouldReturn('dummy/');
    }
    public function it_should_return_true_from_has_dirs_when_there_is_at_less_one(): void
    {
        $this->initAll('/')
             ->hasDirs()
             ->shouldReturn(false);
        $paths = [
            'dummy',
            'http:///dummy/',
            'vfs::\\\\C:\\dummy',
            'ftp://vfs:///dummy/dir'
        ];
        foreach ($paths as $path) {
            $this->initAll($path)
                 ->hasDirs()
                 ->shouldReturn(true);
        }
    }
    public function it_should_return_true_from_has_wrappers_when_there_are_wrappers(): void
    {
        $this->initAll('dummy/')
             ->hasWrappers()
             ->shouldReturn(false);
        $paths = [
            'ftp://dummy',
            'http:///dummy/',
            'vfs::\\\\C:\\dummy',
            'ftp://vfs:///dummy/'
        ];
        foreach ($paths as $path) {
            $this->initAll($path)
                 ->hasWrappers()
                 ->shouldReturn(true);
        }
    }
    public function it_should_return_true_from_is_absolute_path_when_it_is(): void
    {
        $this->initAll('dummy/')
             ->isAbsolutePath()
             ->shouldReturn(false);
        $paths = [
            'ftp:///dummy',
            'http:///dummy/',
            'vfs::\\\\C:\\dummy',
            'ftp://vfs:///dummy/'
        ];
        foreach ($paths as $path) {
            $this->initAll($path)
                 ->isAbsolutePath()
                 ->shouldReturn(true);
        }
    }
    public function it_throws_exception_for_empty_path_from_init_all(): void
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
    public function it_throws_exception_for_illegal_characters_in_path_from_init_all(): void
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
    public function let(): void
    {
        $this->beConstructedWith('dummy');
    }
}
