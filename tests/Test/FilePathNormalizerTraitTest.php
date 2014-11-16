<?php
/**
 * FilePathNormalizerTraitTest.php
 *
 * PHP version 5.4
 *
 * @since  20141115 22:33
 * @author Michael Cummings <mgcummings@yahoo.com>
 */
namespace FilePathNormalizer\Test;

use FilePathNormalizer\FilePathNormalizer;
use FilePathNormalizer\FilePathNormalizerTrait;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Class FilePathNormalizerTraitTest
 */
class FilePathNormalizerTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testFpnIsEmpty()
    {
        $fpnt = $this->getMockForTrait('FilePathNormalizer\FilePathNormalizerTrait');
        $this->assertAttributeEmpty('fpn', $fpnt);
        return $fpnt;
    }
    /**
     * @depends testFpnIsEmpty
     *
     * @param PHPUnit_Framework_MockObject_MockObject $fpnt
     */
    public function testSetFpnValue(
        PHPUnit_Framework_MockObject_MockObject $fpnt
    ) {
        /**
         * @type FilePathNormalizerTrait $fpnt
         */
        $fpn = new FilePathNormalizer();
        $fpnt->setFpn($fpn);
        $this->assertAttributeNotEmpty('fpn', $fpnt);
        $this->assertAttributeInstanceOf('FilePathNormalizer\FilePathNormalizerInterface',
            'fpn', $fpnt);
        $this->assertAttributeSame($fpn, 'fpn', $fpnt);
    }
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }
}
