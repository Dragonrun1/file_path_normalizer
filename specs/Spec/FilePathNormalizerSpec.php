<?php
/**
 * Contains FilePathNormalizerSpec class.
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

use FilePathNormalizer\FilePathNormalizerInterface;
use PhpSpec\ObjectBehavior;

/**
 * Class FilePathNormalizerSpec
 *
 * @mixin \FilePathNormalizer\FilePathNormalizer
 *
 * @method void duringNormalizeFile($value)
 * @method void duringNormalizePath($value)
 */
class FilePathNormalizerSpec extends ObjectBehavior
{
    public function itIsInitializable()
    {
        $this->shouldHaveType('FilePathNormalizer\FilePathNormalizer');
    }
    public function itShouldOverrideAbsoluteRequiredForVfsStreamWhenTryingToNormalizePath()
    {
        $this->normalizePath('vfs://fake/path/')->shouldReturn('vfs://fake/path/');
    }
    public function itShouldStillAllowLegacyBooleanOptionsParameter()
    {
        $path = 'wrap://C:/fake/path';
        $this->normalizePath($path, true)->shouldReturn($path . '/');
        $path = 'wrap://fake/path';
        $this->normalizePath($path, false)->shouldReturn($path . '/');
    }
    public function itThrowsExceptionForInvalidFileTypesFromNormalizeFile()
    {
        $messages = [
            'array' => [],
            'integer' => 0,
            'NULL' => null
        ];
        foreach ($messages as $mess => $file) {
            $mess = 'String expected but was given ' . $mess;
            $this->shouldThrow(new \InvalidArgumentException($mess))
                ->duringNormalizeFile($file);
        }
    }
    public function itThrowsExceptionForInvalidPathFromNormalizePath()
    {
        $paths = [
            "\r\n",
            '',
            "fake\034path"
        ];
        foreach ($paths as $path) {
            $mess = 'Path can NOT have non-printable characters or be empty';
            $this->shouldThrow(new \DomainException($mess))
                ->duringNormalizePath($path);
        }
    }
    public function itThrowsExceptionForInvalidPathTypesFromNormalizePath()
    {
        $messages = [
            'array' => [],
            'integer' => 0,
            'NULL' => null
        ];
        foreach ($messages as $mess => $path) {
            $mess = 'String expected but was given ' . $mess;
            $this->shouldThrow(new \InvalidArgumentException($mess))
                ->duringNormalizePath($path);
        }
    }
    public function itThrowsExceptionForInvalidWrapperIfWrapperAllowedWhenTryingToNormalizePath()
    {
        $mess = 'Invalid wrapper(s), was given %s';
        $options = FilePathNormalizerInterface::ABSOLUTE_REQUIRED | FilePathNormalizerInterface::WRAPPER_ALLOWED;
        $paths = [
            '123://' => '123://c:/fake/path',
            '_ab://' => '_ab:///fake/path',
            '-ab://' => '-ab:///fake/',
            '+ab://' => ' +ab:///fake/path/',
            'ftp:///a/fake/path/ab://' => 'ftp:///a/fake/path/ab:///a/path/'
        ];
        foreach ($paths as $wrapper => $path) {
            $this->shouldThrow(new \DomainException(sprintf($mess, $wrapper)))->duringNormalizePath($path, $options);
        }
    }
    public function itThrowsExceptionForInvalidWrapperIfWrapperRequiredWhenTryingToNormalizePath()
    {
        $mess = 'Invalid wrapper(s), was given %s';
        $options = FilePathNormalizerInterface::ABSOLUTE_REQUIRED | FilePathNormalizerInterface::WRAPPER_REQUIRED;
        $paths = [
            '123://' => '123://c:/fake/path',
            '_ab://' => '_ab:///fake/path',
            '-ab://' => '-ab:///fake/',
            '+ab://' => ' +ab:///fake/path/',
            'ftp:///a/fake/path/ab://' => 'ftp:///a/fake/path/ab:///a/path/'
        ];
        foreach ($paths as $wrapper => $path) {
            $this->shouldThrow(new \DomainException(sprintf($mess, $wrapper)))->duringNormalizePath($path, $options);
        }
    }
    public function itThrowsExceptionForMissingWrapperIfWrapperRequiredWhenTryingToNormalizePath()
    {
        $mess = 'Missing wrapper(s) when required set';
        $options = FilePathNormalizerInterface::ABSOLUTE_REQUIRED | FilePathNormalizerInterface::WRAPPER_REQUIRED;
        $paths = ['c:/fake/path', '/fake/path'];
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))->duringNormalizePath($path, $options);
        }
    }
    public function itThrowsExceptionForProvidedWrapperIfWrapperDisabledWhenTryingToNormalizePath()
    {
        $mess = 'Given wrapper(s) when wrapper disabled';
        $options = FilePathNormalizerInterface::ABSOLUTE_REQUIRED | FilePathNormalizerInterface::WRAPPER_DISABLED;
        $paths = ['wrap://c:/fake/path', 'wrap:///fake/path'];
        foreach ($paths as $path) {
            $this->shouldThrow(new \DomainException($mess))->duringNormalizePath($path, $options);
        }
    }
    public function itThrowsExceptionForRelativePathIfAbsoluteRequiredWhenTryingToNormalizePath()
    {
        $path = 'fake/path/';
        $mess = 'Absolute path required but root part missing, was given '
            . $path;
        $this->shouldThrow(new \DomainException($mess))
            ->duringNormalizePath($path);
    }
}
