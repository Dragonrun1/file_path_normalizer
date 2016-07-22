<?php
declare(strict_types = 1);
/**
 * Contains class PathInfo.
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
 * <http://spdx.org/licenses/GPL-2.0.html>.
 *
 * You should be able to find a copy of this license in the LICENSE file.
 *
 * @author    Michael Cummings <mgcummings@yahoo.com>
 * @copyright 2016 Michael Cummings
 * @license   GPL-2.0
 */
namespace FilePathNormalizer;

/**
 * Class PathInfo.
 *
 * @since 2.0.0-dev New path info awareness.
 */
class PathInfo implements PathInfoInterface
{
    /**
     * PathInfo constructor.
     *
     * @param string $path
     *
     * @throws \DomainException
     */
    public function __construct(string $path)
    {
        $this->initAll($path);
    }
    /**
     * Provides a lightly cleaned up array of the directory path parts without wrappers or root.
     *
     * The light cleaning done is to remove any leading or trailing whitespace chars,
     * remove any leading or trailing directory separators to prevent empty parts,
     * next remove any consecutive directory separators to prevent the empty
     * parts they would create,
     * and finally explode the path into an array of parts to be returned.
     *
     * @return array
     */
    public function getDirList(): array
    {
        $path = str_replace('\\', '/', trim($this->dirs));
        // Drop any leading and trailing "/"s.
        $path = trim($path, '/');
        // Drop pointless consecutive "/"s.
        while (false !== strpos($path, '//')) {
            $path = str_replace('//', '/', $path);
        }
        if ('' !== $path) {
            return explode('/', $path);
        }
        return [];
    }
    /**
     * @return string
     */
    public function getDirs(): string
    {
        return $this->dirs;
    }
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    /**
     * @return string
     */
    public function getRoot(): string
    {
        return $this->root;
    }
    /**
     * @return array
     */
    public function getWrapperList(): array
    {
        $wrappers = explode('://', $this->wrappers);
        // Discard empty artifact.
        array_pop($wrappers);
        return $wrappers;
    }
    /**
     * @return string
     */
    public function getWrappers(): string
    {
        return $this->wrappers;
    }
    /**
     * @return bool
     */
    public function hasDirs(): bool
    {
        return (bool)strlen($this->dirs);
    }
    /**
     * @return bool
     */
    public function hasWrappers(): bool
    {
        return (bool)strlen($this->wrappers);
    }
    /**
     * @param string $path
     *
     * @return $this Fluent interface.
     * @throws \DomainException
     */
    public function initAll(string $path)
    {
        $this->path = $path;
        list($this->wrappers, $this->root, $this->dirs) = $this->getPathParts();
        return $this;
    }
    /**
     * @return bool
     */
    public function isAbsolutePath(): bool
    {
        return (bool)strlen($this->root);
    }
    /**
     * @return array
     * @throws \DomainException
     */
    protected function getPathParts(): array
    {
        $path = trim($this->path);
        if ('' === $path) {
            $mess = 'An empty path is NOT allowed';
            throw new \DomainException($mess);
        }
        if (!ctype_print($path)) {
            $mess = 'Using any non-printable characters in the path is NOT allowed';
            throw new \DomainException($mess);
        }
        $path = str_replace('\\', '/', $path);
        // Optional wrapper(s).
        $regExp = '%^(?<w>(?:[[:print:]]{2,}://)*)';
        // Optional root prefix.
        $regExp .= '(?<r>(?:[[:alpha:]]:/|/)?)';
        // Actual path.
        $regExp .= '(?<p>(?:.*))$%';
        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = 'Path is invalid for unknown reason. Please report this bug.';
            throw new \DomainException($mess);
        }
        return [$parts['w'], $parts['r'], $parts['p']];
    }
    /**
     * @var string $dirs
     */
    private $dirs;
    /**
     * @var string $path
     */
    private $path;
    /**
     * @var string $root
     */
    private $root;
    /**
     * @var string $wrappers
     */
    private $wrappers;
}
