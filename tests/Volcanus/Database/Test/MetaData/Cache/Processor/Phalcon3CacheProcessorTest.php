<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache\Processor;

use Volcanus\Database\Test\MetaData\Cache\AbstractCacheProcessorTest;
use Volcanus\Database\MetaData\Cache\Processor\Phalcon3CacheProcessor;

/**
 * Test for Phalcon3CacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class Phalcon3CacheProcessorTest extends AbstractCacheProcessorTest
{

    public function setUp()
    {
        if (!class_exists('\Phalcon\Version')) {
            $this->markTestSkipped('needs \Phalcon.');
        }
        if (version_compare(\Phalcon\Version::get(), '4.0.0', '>=')) {
            $this->markTestSkipped(
                'A target of this test is Phalcon 3.'
            );
        }
        parent::setUp();
    }

    private function createCacheProvider()
    {
        return new \Phalcon\Cache\Backend\File(
            new \Phalcon\Cache\Frontend\Data(), ['cacheDir' => $this->cacheDir]
        );
    }

    public function testSetAndGetMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new Phalcon3CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new Phalcon3CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new Phalcon3CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new Phalcon3CacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

    public function testSetAndGetMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Phalcon\Cache\BackendInterface|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\BackendInterface::class);
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaTables));

        $cacheProcessor = new Phalcon3CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    public function testUnsetAndNotHasMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider \Phalcon\Cache\BackendInterface|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\BackendInterface::class);
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

        $cacheProcessor = new Phalcon3CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    public function testSetAndGetMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Phalcon\Cache\BackendInterface|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\BackendInterface::class);
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaColumns));

        $cacheProcessor = new Phalcon3CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    public function testUnsetAndNotHasMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider \Phalcon\Cache\BackendInterface|\PHPUnit_Framework_MockObject_MockObject */
        $cacheProvider = $this->createMock(\Phalcon\Cache\BackendInterface::class);
        $cacheProvider->expects($this->once())
            ->method('save')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(false));

        $cacheProcessor = new Phalcon3CacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

}
