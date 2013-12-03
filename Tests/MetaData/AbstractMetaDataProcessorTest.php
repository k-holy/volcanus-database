<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\MetaData;

use Volcanus\Database\MetaData\AbstractMetaDataProcessor;
use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Statement;
use Volcanus\Database\Table;
use Volcanus\Database\Column;

/**
 * Test for AbstractMetaDataProcessor
 *
 * @author k.holy74@gmail.com
 */
class AbstractMetaDataProcessorTest extends \PHPUnit_Framework_TestCase
{

	public function testGetMetaTables()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaTables = $metaDataProcessor->buildMetaTables();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$this->assertEquals($metaTables, $metaDataProcessor->getMetaTables($driverInterface));
	}

	public function testGetMetaTablesFromCache()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaTables = $metaDataProcessor->buildMetaTables();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaTables')
			->will($this->returnValue(true));
		$cacheProcessorInterface->expects($this->once())
			->method('getMetaTables')
			->will($this->returnValue($metaTables));

		$metaDataProcessor->setCacheProcessor($cacheProcessorInterface);

		$this->assertEquals($metaTables, $metaDataProcessor->getMetaTables($driverInterface));
	}

	public function testGetMetaTablesSaveToCache()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaTables = $metaDataProcessor->buildMetaTables();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaTables')
			->will($this->returnValue(false));
		$cacheProcessorInterface->expects($this->once())
			->method('setMetaTables')
			->with($this->equalTo($metaTables));

		$metaDataProcessor->setCacheProcessor($cacheProcessorInterface);

		$this->assertEquals($metaTables, $metaDataProcessor->getMetaTables($driverInterface));
	}

	public function testGetMetaColumns()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaColumns = $metaDataProcessor->buildMetaColumns();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$this->assertEquals($metaColumns, $metaDataProcessor->getMetaColumns($driverInterface, 'users'));
	}

	public function testGetMetaColumnsFromCache()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaColumns = $metaDataProcessor->buildMetaColumns();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaColumns')
			->will($this->returnValue(true));
		$cacheProcessorInterface->expects($this->once())
			->method('getMetaColumns')
			->will($this->returnValue($metaColumns));

		$metaDataProcessor->setCacheProcessor($cacheProcessorInterface);

		$this->assertEquals($metaColumns, $metaDataProcessor->getMetaColumns($driverInterface, 'users'));
	}

	public function testGetMetaColumnsSaveToCache()
	{
		$metaDataProcessor = new TestMetaDataProcessor();

		$metaColumns = $metaDataProcessor->buildMetaColumns();

		$driverInterface = $this->getMock('\\Volcanus\Database\Driver\DriverInterface');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaColumns')
			->will($this->returnValue(false));
		$cacheProcessorInterface->expects($this->once())
			->method('setMetaColumns')
			->with(
				$this->equalTo('users'),
				$this->equalTo($metaColumns)
			);

		$metaDataProcessor->setCacheProcessor($cacheProcessorInterface);

		$this->assertEquals($metaColumns, $metaDataProcessor->getMetaColumns($driverInterface, 'users'));
	}

}

class TestMetaDataProcessor extends AbstractMetaDataProcessor
{

	protected function doGetMetaTables(DriverInterface $driver)
	{
		return $this->buildMetaTables();
	}

	protected function doGetMetaColumns(DriverInterface $driver, $table)
	{
		return $this->buildMetaColumns();
	}

	public function buildMetaTables()
	{
		return array(
			new Table(array(
				'name' => 'users',
				'comment' => 'Table of Users',
				'columns' => $this->buildMetaColumns(),
			)),
		);
	}

	public function buildMetaColumns()
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
