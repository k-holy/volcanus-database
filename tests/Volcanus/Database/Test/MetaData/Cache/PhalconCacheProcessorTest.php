<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData\Cache;

use Volcanus\Database\MetaData\Cache\PhalconCacheProcessor;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;

/**
 * Test for PhalconCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class PhalconCacheProcessorTest extends \PHPUnit_Framework_TestCase
{

	public function testHasMetaTables()
	{
		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('exists')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->hasMetaTables());
	}

	public function testGetMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('get')
			->will($this->returnValue($metaTables));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertEquals($metaTables, $cache->getMetaTables());
	}

	public function testSetMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->setMetaTables('users', $metaTables));
	}

	public function testUnsetMetaTables()
	{
		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->unsetMetaTables());
	}

	public function testHasMetaColumns()
	{
		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('exists')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->hasMetaColumns('users'));
	}

	public function testGetMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('get')
			->will($this->returnValue($metaColumns));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertEquals($metaColumns, $cache->getMetaColumns('users'));
	}

	public function testSetMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->setMetaColumns('users', $metaColumns));
	}

	public function testUnsetMetaColumns()
	{
		$phalconCacheBackendInterface = $this->getMock('\\Phalcon\\Cache\\BackendInterface');
		$phalconCacheBackendInterface->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));

		$cache = new PhalconCacheProcessor($phalconCacheBackendInterface);
		$this->assertTrue($cache->unsetMetaColumns('users'));
	}

	private function buildMetaTables()
	{
		return array(
			new Table(array(
				'name' => 'users',
				'comment' => 'Table of Users',
				'columns' => $this->buildMetaColumns(),
			)),
		);
	}

	private function buildMetaColumns()
	{
		return array(
			'id' => new Column(array(
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
			)),
			'name' => new Column(array(
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
			)),
		);
	}

}
