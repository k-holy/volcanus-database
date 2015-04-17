<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache;

use Volcanus\Database\MetaData\Cache\PhalconCacheProcessor;

/**
 * Test for PhalconCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class PhalconCacheProcessorTest extends AbstractCacheProcessorTest
{

	private function createCacheProvider()
	{
		return new \Phalcon\Cache\Backend\File(
			new \Phalcon\Cache\Frontend\Data(), ['cacheDir' => $this->cacheDir]
		);
	}

	public function testSetAndGetMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$cacheProcessor = new PhalconCacheProcessor($this->createCacheProvider());
		$this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
		$this->assertTrue($cacheProcessor->hasMetaTables());
		$this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
	}

	public function testUnsetAndNotHasMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$cacheProcessor = new PhalconCacheProcessor($this->createCacheProvider());
		$this->assertTrue($cacheProcessor->setMetaTables($metaTables, 86400));
		$this->assertTrue($cacheProcessor->unsetMetaTables());
		$this->assertFalse($cacheProcessor->hasMetaTables());
	}

	public function testSetAndGetMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$cacheProcessor = new PhalconCacheProcessor($this->createCacheProvider());
		$this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
		$this->assertTrue($cacheProcessor->hasMetaColumns('users'));
		$this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
	}

	public function testUnsetAndNotHasMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$cacheProcessor = new PhalconCacheProcessor($this->createCacheProvider());
		$this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns, 86400));
		$this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
		$this->assertFalse($cacheProcessor->hasMetaColumns('users'));
	}

	public function testSetAndGetMetaTablesByMock()
	{
		$metaTables = $this->buildMetaTables();

		$cacheProvider = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$cacheProvider->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('exists')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('get')
			->will($this->returnValue($metaTables));

		$cacheProcessor = new PhalconCacheProcessor($cacheProvider);
		$this->assertTrue($cacheProcessor->setMetaTables($metaTables));
		$this->assertTrue($cacheProcessor->hasMetaTables());
		$this->assertEquals($metaTables, $cacheProcessor->getMetaTables());
	}

	public function testUnsetAndNotHasMetaTablesByMock()
	{
		$metaTables = $this->buildMetaTables();

		$cacheProvider = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$cacheProvider->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('exists')
			->will($this->returnValue(false));

		$cacheProcessor = new PhalconCacheProcessor($cacheProvider);
		$this->assertTrue($cacheProcessor->setMetaTables($metaTables));
		$this->assertTrue($cacheProcessor->unsetMetaTables());
		$this->assertFalse($cacheProcessor->hasMetaTables());
	}

	public function testSetAndGetMetaColumnsByMock()
	{
		$metaColumns = $this->buildMetaColumns();

		$cacheProvider = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$cacheProvider->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('exists')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('get')
			->will($this->returnValue($metaColumns));

		$cacheProcessor = new PhalconCacheProcessor($cacheProvider);
		$this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
		$this->assertTrue($cacheProcessor->hasMetaColumns('users'));
		$this->assertEquals($metaColumns, $cacheProcessor->getMetaColumns('users'));
	}

	public function testUnsetAndNotHasMetaColumnsByMock()
	{
		$metaColumns = $this->buildMetaColumns();

		$cacheProvider = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$cacheProvider->expects($this->once())
			->method('save')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));
		$cacheProvider->expects($this->once())
			->method('exists')
			->will($this->returnValue(false));

		$cacheProcessor = new PhalconCacheProcessor($cacheProvider);
		$this->assertTrue($cacheProcessor->setMetaColumns('users', $metaColumns));
		$this->assertTrue($cacheProcessor->unsetMetaColumns('users'));
		$this->assertFalse($cacheProcessor->hasMetaColumns('users'));
	}

}
