<?php
/**
 * Created by PhpStorm.
 * User: Dragonaire
 * Date: 7/19/2016
 * Time: 11:25 PM
 */
namespace FilePathNormalizer;

/**
 * Class PathInfo.
 *
 * @since 2.0.0-dev New path info awareness.
 */
interface PathInfoInterface
{
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
    public function getDirList() : array;
    /**
     * @return string
     */
    public function getDirs() : string;
    /**
     * @return string
     */
    public function getPath() : string;
    /**
     * @return string
     */
    public function getRoot() : string;
    /**
     * @return array
     */
    public function getWrapperList() : array;
    /**
     * @return string
     */
    public function getWrappers() : string;
    /**
     * @return bool
     */
    public function hasDirs() : bool;
    /**
     * @return bool
     */
    public function hasWrappers() : bool;
    /**
     * @param string $path
     *
     * @return $this Fluent interface.
     */
    public function initAll(string $path);
    /**
     * @return bool
     */
    public function isAbsolutePath() : bool;
}
