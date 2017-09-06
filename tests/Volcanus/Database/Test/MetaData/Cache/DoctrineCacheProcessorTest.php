<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache;

use Volcanus\Database\MetaData\Cache\DoctrineCacheProcessor;

/**
 * Test for DoctrineCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class DoctrineCacheProcessorTest extends AbstractCacheProcessorTest
{

    private function createCacheProvider()
    {
        return new \Doctrine\Common\Cache\PhpFileCache($this->cacheDir);
    }

    public function testSetAndGetMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new DoctrineCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new DoctrineCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new DoctrineCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new DoctrineCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

    public function testSetAndGetMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock('\Doctrine\Common\Cache\Cache');
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($metaTables));

        $cacheProcessor = new DoctrineCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock('\Doctrine\Common\Cache\Cache');
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(false));

        $cacheProcessor = new DoctrineCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock('\Doctrine\Common\Cache\Cache');
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('fetch')
            ->will($this->returnValue($metaColumns));

        $cacheProcessor = new DoctrineCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Doctrine\Common\Cache\Cache|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock('\Doctrine\Common\Cache\Cache');
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('contains')
            ->will($this->returnValue(false));

        $cacheProcessor = new DoctrineCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

}
