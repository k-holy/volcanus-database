<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache\Processor;

use Volcanus\Database\Test\MetaData\Cache\AbstractCacheProcessorTest;
use Volcanus\Database\MetaData\Cache\Processor\Phalcon4CacheProcessor;

/**
 * Test for Phalcon4CacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class Phalcon4CacheProcessorTest extends AbstractCacheProcessorTest
{

    public function setUp(): void
    {
        if (!class_exists('\Phalcon\Version')) {
            $this->markTestSkipped('needs \Phalcon.');
        }
        if (version_compare(\Phalcon\Version::get(), '4.0.0', '<')) {
            $this->markTestSkipped(
                'A target of this test is Phalcon 4.'
            );
        }
        parent::setUp();
    }

    private function createCacheProvider()
    {
        $factory = new \Phalcon\Storage\SerializerFactory();
        return new \Phalcon\Cache\Adapter\Memory($factory);
    }

    public function testSetAndGetMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new Phalcon4CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new Phalcon4CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new Phalcon4CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new Phalcon4CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

    public function testSetAndGetMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Phalcon\Cache\Adapter\AdapterInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\Adapter\AdapterInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaTables));

        $cacheProcessor = new Phalcon4CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Phalcon\Cache\Adapter\AdapterInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\Adapter\AdapterInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $cacheProcessor = new Phalcon4CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Phalcon\Cache\Adapter\AdapterInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\Adapter\AdapterInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaColumns));

        $cacheProcessor = new Phalcon4CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Phalcon\Cache\Adapter\AdapterInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\Adapter\AdapterInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $cacheProcessor = new Phalcon4CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

}
