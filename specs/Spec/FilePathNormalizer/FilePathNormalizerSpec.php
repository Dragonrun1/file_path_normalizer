<?php
declare(strict_types=1);
/**
 * Contains FilePathNormalizerSpec class.
 *
 * PHP version 7.1
 *
 * LICENSE:
 * This file is part of file_path_normalizer which is used to normalize PHP file
 * paths without several of the shortcomings of the built-in functions.
 * Copyright (C) 2015-2018 Michael Cummings
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

use FilePathNormalizer\FilePathNormalizer;
use FilePathNormalizer\FilePathNormalizerInterface;
use FilePathNormalizer\PathInfoInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FilePathNormalizerSpec
 *
 * @mixin \FilePathNormalizer\FilePathNormalizer
 *
 * @method void during($method, array $params)
 * @method void shouldBe($value)
 * @method void shouldBeCalled()
 * @method void shouldContain($value)
 * @method void shouldHaveKey($key)
 * @method void shouldNotEqual($value)
 * @method void shouldReturn($result)
 */
class FilePathNormalizerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FilePathNormalizer::class);
    }
    public function it_should_return_correctly_converted_path_from_normalize_path(PathInfoInterface $pathInfo): void
    {
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $options = FilePathNormalizerInterface::ABSOLUTE_ALLOWED | FilePathNormalizerInterface::VFS_DISABLED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $paths = [
            'c:\\dummy' =>
                ['wrappers' => [], 'root' => 'c:/', 'dirs' => ['dummy'], 'result' => 'c:/dummy/'],
            'c:\\dummy\\' =>
                ['wrappers' => [], 'root' => 'c:/', 'dirs' => ['dummy'], 'result' => 'c:/dummy/'],
            '/dummy' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy'], 'result' => '/dummy/'],
            '/dummy/' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy'], 'result' => '/dummy/'],
            'c:\\dummy\\path' =>
                ['wrappers' => [], 'root' => 'c:/', 'dirs' => ['dummy', 'path'], 'result' => 'c:/dummy/path/'],
            'c:\\dummy\\path\\' =>
                ['wrappers' => [], 'root' => 'c:/', 'dirs' => ['dummy', 'path'], 'result' => 'c:/dummy/path/'],
            '/dummy/path' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy', 'path'], 'result' => '/dummy/path/'],
            '/dummy/path/' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy', 'path'], 'result' => '/dummy/path/'],
            'dummy' =>
                ['wrappers' => [], 'root' => '', 'dirs' => ['dummy'], 'result' => 'dummy/'],
            'dummy\\' =>
                ['wrappers' => [], 'root' => '', 'dirs' => ['dummy'], 'result' => 'dummy/'],
            'dummy/' =>
                ['wrappers' => [], 'root' => '', 'dirs' => ['dummy'], 'result' => 'dummy/'],
            'ftp://dummy' =>
                ['wrappers' => ['ftp'], 'root' => '', 'dirs' => ['dummy'], 'result' => 'ftp://dummy/'],
            'ftp://dummy\\' =>
                ['wrappers' => ['ftp'], 'root' => '', 'dirs' => ['dummy'], 'result' => 'ftp://dummy/'],
            'ftp://dummy/' =>
                ['wrappers' => ['ftp'], 'root' => '', 'dirs' => ['dummy'], 'result' => 'ftp://dummy/'],
            'ftp://c:\\dummy' =>
                ['wrappers' => ['ftp'], 'root' => 'c:/', 'dirs' => ['dummy'], 'result' => 'ftp://c:/dummy/'],
            '/dummy/../path' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy', '..', 'path'], 'result' => '/path/'],
            '/dummy/./path' =>
                ['wrappers' => [], 'root' => '/', 'dirs' => ['dummy', '.', 'path'], 'result' => '/dummy/path/']
        ];
        foreach ($paths as $given => $expected) {
            $pathInfo->getWrapperList()
                     ->willReturn($expected['wrappers']);
            $pathInfo->hasWrappers()
                     ->willReturn((bool)\count($expected['wrappers']));
            $pathInfo->getRoot()
                     ->willReturn($expected['root']);
            $pathInfo->getDirList()
                     ->willReturn($expected['dirs']);
            $this->setPathInfo($pathInfo);
            $this->normalizePath($given, $options)
                 ->shouldReturn($expected['result']);
        }
    }
    public function it_throws_exception_for_duplicate_wrappers_from_normalize_x(PathInfoInterface $pathInfo): void
    {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrapperList()
                 ->willReturn(['ftp', 'vfs', 'ftp']);
        $pathInfo->hasWrappers()
                 ->willReturn(true);
        $paths = [
            'ftp://vfs://ftp://c:/dummy',
            'ftp://vfs://ftp:///dummy',
            'ftp://vfs://ftp://dummy'
        ];
        $options = FilePathNormalizerInterface::VFS_ALLOWED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $mess = 'Duplicate wrappers are not allowed';
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'ftp://vfs://ftp://c:/dummy/dummy.txt',
            'ftp://vfs://ftp:///dummy/dummy.txt',
            'ftp://vfs://ftp://dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_empty_dir_from_normalize_file(): void
    {
        $paths = ['dummy'];
        $mess = 'An empty path is NOT allowed';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path]);
        }
    }
    public function it_throws_exception_for_empty_file_name_from_normalize_file(): void
    {
        $paths = [
            '/dummy/',
            '/dummy/ ',
            '/dummy/  '
        ];
        $mess = 'An empty file name is NOT allowed';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path]);
        }
    }
    public function it_throws_exception_for_excessive_up_directories(): void
    {
        $options = FilePathNormalizerInterface::ABSOLUTE_ALLOWED | FilePathNormalizerInterface::VFS_DISABLED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $paths = [
            '/dummy/../..',
            'dummy/../..',
            'c:/dummy/../..',
            'dummy/dummy/../../..'
        ];
        $mess = 'Unusual above root path found';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizePath', [$path, $options]);
        }
    }
    public function it_throws_exception_for_forbidden_option_combinations_from_normalize_x(): void
    {
        $options = FilePathNormalizerInterface::VFS_REQUIRED | FilePathNormalizerInterface::ABSOLUTE_REQUIRED;
        $mess = 'Can not use required or allowed options together with corresponding disabled option';
        $this->shouldThrow(new \DomainException($mess))
             ->during('normalizePath', ['dummy', $options]);
    }
    public function it_throws_exception_for_having_absolute_path_when_absolute_is_disabled_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrapperList()
                 ->willReturn([]);
        $pathInfo->hasWrappers()
                 ->willReturn(false);
        $pathInfo->isAbsolutePath()
                 ->willReturn(true);
        $paths = [
            'c:/dummy' => 'c:/',
            '/dummy' => '/'
        ];
        $mess = 'Given absolute path when absolute was disabled';
        $options = FilePathNormalizerInterface::ABSOLUTE_DISABLED;
        foreach ($paths as $path => $root) {
            $pathInfo->getRoot()
                ->willReturn($root);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'c:/dummy/dummy.txt' => 'c:/',
            '/dummy/dummy.txt' => '/'
        ];
        foreach ($paths as $path => $root) {
            $pathInfo->getRoot()
                     ->willReturn($root);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_having_vfsStream_wrapper_when_vfs_is_disabled_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrapperList()
                 ->willReturn(['vfs']);
        $pathInfo->hasWrappers()
                 ->willReturn(true);
        $paths = [
            'vfs://c:/dummy',
            'vfs:///dummy',
            'vfs://dummy'
        ];
        $options = FilePathNormalizerInterface::VFS_DISABLED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $mess = 'Found vfsStream wrapper when it was disabled';
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'vfs://c:/dummy/dummy.txt',
            'vfs:///dummy/dummy.txt',
            'vfs://dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_having_wrapper_when_wrapper_is_disabled_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrappers()
                 ->willReturn('vfs://');
        $pathInfo->hasWrappers()
                 ->willReturn(true);
        $paths = [
            'vfs://c:/dummy',
            'vfs:///dummy',
            'vfs://dummy'
        ];
        $mess = 'Given wrapper when wrapper(s) are disabled';
        $options = FilePathNormalizerInterface::WRAPPER_DISABLED;
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'vfs://c:/dummy/dummy.txt',
            'vfs:///dummy/dummy.txt',
            'vfs://dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_illegal_characters_in_file_name_from_normalize_file(): void
    {
        $paths = [
            "/dummy/\034",
            "/dummy/ \034",
            "/dummy/\034  "
        ];
        $mess = 'Using any non-printable characters in the file name is NOT allowed';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path]);
        }
    }
    public function it_throws_exception_for_invalid_formatted_wrapper_names_from_normalize_x(PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->hasWrappers()
                 ->willReturn(true);
        $paths = [
            '_ftp://vfs://ftp://c:/dummy' => ['_ftp', 'vfs', 'ftp'],
            '_ftp://vfs://ftp:///dummy' => ['_ftp', 'vfs', 'ftp'],
            '_ftp://vfs://ftp://dummy' => ['_ftp', 'vfs', 'ftp'],
            'ft-p://vfs://ftp://c:/dummy' => ['ft-p', 'vfs', 'ftp'],
            'ft-p://vfs://ftp:///dummy' => ['ft-p', 'vfs', 'ftp'],
            'ft-p://vfs://ftp://dummy' => ['ft-p', 'vfs', 'ftp']
        ];
        $options = FilePathNormalizerInterface::VFS_ALLOWED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $mess = 'Invalidly formatted wrapper name found';
        foreach ($paths as $path => $wrappers) {
            $pathInfo->getWrapperList()
                ->willReturn($wrappers);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            '_ftp://vfs://ftp://c:/dummy/dummy.txt',
            '_ftp://vfs://ftp:///dummy/dummy.txt',
            '_ftp://vfs://ftp://dummy/dummy.txt',
            'ft-p://vfs://ftp://c:/dummy/dummy.txt',
            'ft-p://vfs://ftp:///dummy/dummy.txt',
            'ft-p://vfs://ftp://dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            $pathInfo->getWrappers()
                     ->willReturn(substr($path, 0, 19));
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_missing_absolute_path_when_absolute_is_required_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrapperList()
                 ->willReturn([]);
        $pathInfo->hasWrappers()
                 ->willReturn(false);
        $pathInfo->getRoot()
                 ->willReturn('');
        $pathInfo->isAbsolutePath()
                 ->willReturn(false);
        $this->setPathInfo($pathInfo);
        $paths = ['dummy'];
        $mess = 'Absolute path required but root part missing';
        $options = FilePathNormalizerInterface::ABSOLUTE_REQUIRED;
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizePath', [$path, $options]);
        }
        $paths = ['dummy/dummy.txt'];
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_missing_vfsStream_wrapper_when_vfs_is_required_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrapperList()
                 ->willReturn([]);
        $pathInfo->hasWrappers()
                 ->willReturn(false);
        $this->setPathInfo($pathInfo);
        $paths = [
            'c:/dummy',
            '/dummy',
            'dummy'
        ];
        $options = FilePathNormalizerInterface::VFS_REQUIRED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $mess = 'Missing vfsStream wrapper when it was required';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'c:/dummy/dummy.txt',
            '/dummy/dummy.txt',
            'dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_missing_wrapper_when_wrapper_is_required_from_normalize_x(
        PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->getWrappers()
                 ->willReturn('');
        $pathInfo->hasWrappers()
                 ->willReturn(false);
        $this->setPathInfo($pathInfo);
        $paths = [
            'c:/dummy',
            '/dummy',
            'dummy'
        ];
        $options = FilePathNormalizerInterface::WRAPPER_REQUIRED;
        $mess = 'Missing wrapper when wrapper(s) are required';
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'c:/dummy/dummy.txt',
            '/dummy/dummy.txt',
            'dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function it_throws_exception_for_vfsStream_wrapper_not_last_one_from_normalize_x(PathInfoInterface $pathInfo
    ): void {
        /**
         * @var PathInfoInterface $pathInfo
         */
        /** @noinspection PhpStrictTypeCheckingInspection */
        $pathInfo->initAll(Argument::any())
                 ->shouldBeCalled();
        $pathInfo->hasWrappers()
                 ->willReturn(true);
        $paths = [
            'vfs://ftp://c:/dummy' => ['vfs', 'ftp'],
            'vfs://ftp:///dummy' => ['vfs', 'ftp'],
            'vfs://ftp://dummy' => ['vfs', 'ftp']
        ];
        $options = FilePathNormalizerInterface::VFS_ALLOWED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $mess = 'Must use vfsStream as last wrapper';
        foreach ($paths as $path => $wrappers) {
            $pathInfo->getWrapperList()
                ->willReturn($wrappers);
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                ->during('normalizePath', [$path, $options]);
        }
        $paths = [
            'vfs://ftp://c:/dummy/dummy.txt',
            'vfs://ftp:///dummy/dummy.txt',
            'vfs://ftp://dummy/dummy.txt',
            'vfs://ftp://c:/dummy/dummy.txt',
            'vfs://ftp:///dummy/dummy.txt',
            'vfs://ftp://dummy/dummy.txt'
        ];
        foreach ($paths as $path) {
            $pathInfo->getWrappers()
                     ->willReturn(substr($path, 0, 12));
            /** @noinspection DisconnectedForeachInstructionInspection */
            $this->setPathInfo($pathInfo);
            $this->shouldThrow(new \DomainException($mess))
                 ->during('normalizeFile', [$path, $options]);
        }
    }
    public function let($pathInfo): void
    {
        $pathInfo->beADoubleOf(PathInfoInterface::class);
    }
}
