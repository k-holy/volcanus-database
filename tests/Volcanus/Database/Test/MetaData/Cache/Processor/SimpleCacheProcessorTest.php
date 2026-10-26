<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache\Processor;

use Volcanus\Database\MetaData\Column;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Cache\Processor\SimpleCacheProcessor;

use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

/**
 * Test for SimpleCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class SimpleCacheProcessorTest extends \PHPUnit\Framework\TestCase
{

    private string $cacheDir;

    public function setUp(): void
    {
        $this->cacheDir = __DIR__ . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    }

    public function tearDown(): void
    {
        $this->clearDirectory();
    }

    protected function buildMetaTables(): array
    {
        return [
            new Table([
                'name' => 'users',
                'comment' => 'Table of Users',
                'columns' => $this->buildMetaColumns(),
            ]),
        ];
    }

    protected function buildMetaColumns(): array
    {
        return [
            'id' => new Column([
                'name' => 'user_id',
                'type' => 'integer',
                'maxLength' => '11',
                'scale' => null,
                'binary' => false,
                'default' => null,
                'notNull' => true,
                'primaryKey' => true,
                'uniqueKey' => true,
                'autoIncrement' => false,
                'comment' => 'Primary key of User',
            ]),
            'name' => new Column([
                'name' => 'user_name',
                'type' => 'varchar',
                'maxLength' => '255',
                'scale' => null,
                'binary' => false,
                'default' => null,
                'notNull' => true,
                'primaryKey' => false,
                'uniqueKey' => false,
                'autoIncrement' => false,
                'comment' => 'Name of User',
            ]),
        ];
    }

    protected function clearDirectory(): void
    {
        $it = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->cacheDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($it as $file) {
            if ($file->isDir()) {
                if ($file->getRealpath() !== $this->cacheDir) {
                    rmdir($file);
                }
            } elseif ($file->isFile() && $file->getBaseName() !== '.gitignore') {
                unlink($file);
            }
        }
    }

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
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('get')
            ->willReturn($metaTables);

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
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('has')
            ->willReturn(false);

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
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('has')
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('get')
            ->willReturn($metaColumns);

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
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('delete')
            ->willReturn(true);
        $cacheProvider->expects($this->once())
            ->method('has')
            ->willReturn(false);

        $cacheProcessor = new SimpleCacheProcessor($cacheProvider);
        $this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
        $this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
        $this->assertFalse($cacheProcessor->hasMetaColumns('users'));
    }

}
