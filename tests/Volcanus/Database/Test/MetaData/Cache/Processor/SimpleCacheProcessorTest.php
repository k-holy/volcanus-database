<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache\Processor;

use Volcanus\Database\Test\MetaData\Cache\AbstractCacheProcessorTest;
use Volcanus\Database\MetaData\Cache\Processor\SimpleCacheProcessor;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Test for SimpleCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class SimpleCacheProcessorTest extends AbstractCacheProcessorTest
{

    private function createCacheProvider()
    {
        if (version_compare(PHP_VERSION, '7.1.3', '<')) {
            $this->markTestSkipped(
                'A target of this test is PHP 7.1.3~'
            );
        }
        if (class_exists(Psr16Cache::class) &&
            class_exists(FilesystemAdapter::class)
        ) {
            return new Psr16Cache(
                new FilesystemAdapter('', 0, $this->cacheDir)
            );
        } else {
            $this->markTestSkipped(
                'This test required Symfony Cache 4.4~'
            );
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testSetAndGetMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new SimpleCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testUnsetAndNotHasMetaTables()
    {
        $metaTables = $this->buildMetaTables();

        $cacheProcessor = new SimpleCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testSetAndGetMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new SimpleCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testUnsetAndNotHasMetaColumns()
    {
        $metaColumns = $this->buildMetaColumns();

        $cacheProcessor = new SimpleCacheProcessor($this->createCacheProvider());
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testSetAndGetMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider CacheInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(CacheInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaTables));

        $cacheProcessor = new SimpleCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->hasMetaTables());
        $this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testUnsetAndNotHasMetaTablesByMock()
    {
        $metaTables = $this->buildMetaTables();

        /** @var $cacheProvider CacheInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(CacheInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $cacheProcessor = new SimpleCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaTables($metaTables));
        $this->assertTrue($cacheProcessor->unsetMetaTables());
        $this->assertFalse($cacheProcessor->hasMetaTables());
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testSetAndGetMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider CacheInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(CacheInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('get')
            ->will($this->returnValue($metaColumns));

        $cacheProcessor = new SimpleCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->hasMetaColumns('users'));
        $this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testUnsetAndNotHasMetaColumnsByMock()
    {
        $metaColumns = $this->buildMetaColumns();

        /** @var $cacheProvider CacheInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProvider = $this->createMock(CacheInterface::class);
        $cacheProvider->expects($this->once())
            ->method('set')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->will($this->returnValue(true));
        $cacheProvider->expects($this->once())
            ->method('has')
            ->will($this->returnValue(false));

        $cacheProcessor = new SimpleCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

}
