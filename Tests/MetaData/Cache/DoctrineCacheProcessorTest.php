<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\MetaData\Cache;

use Volcanus\Database\MetaData\Cache\DoctrineCacheProcessor;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;

/**
 * Test for DoctrineCacheProcessor
 *
 * @author k.holy74@gmail.com
 */
class DoctrineCacheProcessorTest extends \PHPUnit_Framework_TestCase
{

	public function testHasMetaTables()
	{
		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('contains')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertTrue($cache->hasMetaTables());
	}

	public function testGetMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('fetch')
			->will($this->returnValue($metaTables));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertEquals($metaTables, $cache->getMetaTables());
	}

	public function testSetMetaTables()
	{
		$metaTables = $this->buildMetaTables();

		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertTrue($cache->setMetaTables('users', $metaTables));
	}

	public function testUnsetMetaTables()
	{
		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertTrue($cache->unsetMetaTables());
	}

	public function testHasMetaColumns()
	{
		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('contains')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertTrue($cache->hasMetaColumns('users'));
	}

	public function testGetMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('fetch')
			->will($this->returnValue($metaColumns));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertEquals($metaColumns, $cache->getMetaColumns('users'));
	}

	public function testSetMetaColumns()
	{
		$metaColumns = $this->buildMetaColumns();

		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('save')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
		$this->assertTrue($cache->setMetaColumns('users', $metaColumns));
	}

	public function testUnsetMetaColumns()
	{
		$doctrineCacheInterface = $this->getMock('\\Doctrine\\Common\\Cache\\Cache');
		$doctrineCacheInterface->expects($this->once())
			->method('delete')
			->will($this->returnValue(true));

		$cache = new DoctrineCacheProcessor($doctrineCacheInterface);
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
