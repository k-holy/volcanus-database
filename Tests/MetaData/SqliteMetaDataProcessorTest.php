<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\MetaData;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for SqliteMetaDataProcessor
 *
 * @author k.holy74@gmail.com
 */
class SqliteMetaDataProcessorTest extends \PHPUnit_Framework_TestCase
{

	private static $driver;

	private function getDriver()
	{
		if (!isset(self::$driver)) {
			$pdo = new \PDO('sqlite::memory:');
			$pdo->exec(<<<SQL
CREATE TABLE users(
     user_id      INTEGER       NOT NULL PRIMARY KEY
    ,user_type    INTEGER       NOT NULL DEFAULT 1
    ,user_key     VARCHAR(64)   NOT NULL UNIQUE
    ,user_name    VARCHAR(255)           DEFAULT NULL
    ,user_decimal DECIMAL(10,5)
    ,user_binary  BLOB
);
SQL
			);
			$pdo->exec(<<<SQL
CREATE TABLE messages(
     message_id INTEGER      NOT NULL PRIMARY KEY
    ,title      VARCHAR(255) NOT NULL
    ,message    TEXT
    ,posted_by  INTEGER      NOT NULL
    ,FOREIGN KEY(posted_by) REFERENCES users(user_id) ON DELETE CASCADE
);
SQL
			);
			self::$driver = new PdoDriver($pdo);
		}
		return self::$driver;
	}

	public function testGetMetaTables()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaTables = $metaDataProcessor->getMetaTables($this->getDriver());

		$this->assertArrayHasKey('users', $metaTables);
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Table', $metaTables['users']);

		$this->assertArrayHasKey('messages', $metaTables);
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Table', $metaTables['messages']);
	}

	public function testGetMetaTablesFromCache()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaTablesCache  = $metaDataProcessor->getMetaTables($this->getDriver());

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaTables')
			->will($this->returnValue(true));
		$cacheProcessorInterface->expects($this->once())
			->method('getMetaTables')
			->will($this->returnValue($metaTablesCache));

		$metaDataProcessor = new SqliteMetaDataProcessor($cacheProcessorInterface);

		$this->assertEquals($metaTablesCache, $metaDataProcessor->getMetaTables($this->getDriver()));
	}

	public function testGetMetaTablesSaveToCache()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaTablesCache  = $metaDataProcessor->getMetaTables($this->getDriver());

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaTables')
			->will($this->returnValue(false));
		$cacheProcessorInterface->expects($this->once())
			->method('setMetaTables')
			->with($this->equalTo($metaTablesCache));

		$metaDataProcessor = new SqliteMetaDataProcessor($cacheProcessorInterface);
		$metaDataProcessor->getMetaTables($this->getDriver());
	}

	public function testGetMetaTablesName()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaTables = $metaDataProcessor->getMetaTables($this->getDriver());

		$this->assertEquals('users'   , $metaTables['users']->name);
		$this->assertEquals('messages', $metaTables['messages']->name);
	}

	public function testGetMetaColumns()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertArrayHasKey('user_id', $metaColumns);
		$column = $metaColumns['user_id'];
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Column', $column);
		$this->assertFalse($column->binary);

		$this->assertArrayHasKey('user_type', $metaColumns);
		$column = $metaColumns['user_type'];
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Column', $column);
		$this->assertFalse($column->binary);

		$this->assertArrayHasKey('user_name', $metaColumns);
		$column = $metaColumns['user_name'];
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Column', $column);
		$this->assertFalse($column->binary);
	}

	public function testGetMetaColumnsFromCache()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumnsCache = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaColumns')
			->will($this->returnValue(true));
		$cacheProcessorInterface->expects($this->once())
			->method('getMetaColumns')
			->will($this->returnValue($metaColumnsCache));

		$metaDataProcessor = new SqliteMetaDataProcessor($cacheProcessorInterface);

		$this->assertEquals($metaColumnsCache, $metaDataProcessor->getMetaColumns($this->getDriver(), 'users'));
	}

	public function testGetMetaColumnsSaveToCache()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumnsCache  = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$cacheProcessorInterface = $this->getMock('\\Volcanus\Database\MetaData\Cache\CacheProcessorInterface');
		$cacheProcessorInterface->expects($this->once())
			->method('hasMetaColumns')
			->will($this->returnValue(false));
		$cacheProcessorInterface->expects($this->once())
			->method('setMetaColumns')
			->with(
				$this->equalTo('users'),
				$this->equalTo($metaColumnsCache)
			);

		$metaDataProcessor = new SqliteMetaDataProcessor($cacheProcessorInterface);
		$metaDataProcessor->getMetaColumns($this->getDriver(), 'users');
	}

	public function testGetColumnName()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertEquals('user_id'  , $metaColumns['user_id']->name);
		$this->assertEquals('user_type', $metaColumns['user_type']->name);
		$this->assertEquals('user_name', $metaColumns['user_name']->name);
		$this->assertEquals('user_decimal', $metaColumns['user_decimal']->name);
		$this->assertEquals('user_binary' , $metaColumns['user_binary']->name);
	}

	public function testGetColumnType()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertEquals('INTEGER', $metaColumns['user_id']->type);
		$this->assertEquals('INTEGER', $metaColumns['user_type']->type);
		$this->assertEquals('VARCHAR', $metaColumns['user_key']->type);
		$this->assertEquals('VARCHAR', $metaColumns['user_name']->type);
		$this->assertEquals('DECIMAL', $metaColumns['user_decimal']->type);
		$this->assertEquals('BLOB'   , $metaColumns['user_binary']->type);
	}

	public function testGetColumnMaxLength()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertNull($metaColumns['user_id']->maxLength);
		$this->assertNull($metaColumns['user_type']->maxLength);
		$this->assertEquals('64', $metaColumns['user_key']->maxLength);
		$this->assertEquals('255', $metaColumns['user_name']->maxLength);
		$this->assertEquals('10' , $metaColumns['user_decimal']->maxLength);
		$this->assertNull($metaColumns['user_binary']->maxLength);
	}

	public function testGetColumnScale()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertNull($metaColumns['user_id']->scale);
		$this->assertNull($metaColumns['user_type']->scale);
		$this->assertNull($metaColumns['user_key']->scale);
		$this->assertNull($metaColumns['user_name']->scale);
		$this->assertEquals('5' , $metaColumns['user_decimal']->scale);
		$this->assertNull($metaColumns['user_binary']->scale);
	}

	public function testGetColumnIsBinary()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertFalse($metaColumns['user_id']->binary);
		$this->assertFalse($metaColumns['user_type']->binary);
		$this->assertFalse($metaColumns['user_key']->binary);
		$this->assertFalse($metaColumns['user_name']->binary);
		$this->assertFalse($metaColumns['user_decimal']->binary);
		$this->assertTrue($metaColumns['user_binary']->binary);
	}

	public function testGetColumnDefault()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertNull($metaColumns['user_id']->default);
		$this->assertEquals('1', $metaColumns['user_type']->default);
		$this->assertNull($metaColumns['user_key']->default);
		$this->assertNull($metaColumns['user_name']->default);
		$this->assertNull($metaColumns['user_decimal']->default);
		$this->assertNull($metaColumns['user_binary']->default);
	}

	public function testGetColumnIsNotNull()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertTrue($metaColumns['user_id']->notNull);
		$this->assertTrue($metaColumns['user_type']->notNull);
		$this->assertTrue($metaColumns['user_key']->notNull);
		$this->assertFalse($metaColumns['user_name']->notNull);
		$this->assertFalse($metaColumns['user_decimal']->notNull);
		$this->assertFalse($metaColumns['user_binary']->notNull);
	}

	public function testGetColumnIsPrimaryKey()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertTrue($metaColumns['user_id']->primaryKey);
		$this->assertFalse($metaColumns['user_type']->primaryKey);
		$this->assertFalse($metaColumns['user_key']->primaryKey);
		$this->assertFalse($metaColumns['user_name']->primaryKey);
		$this->assertFalse($metaColumns['user_decimal']->primaryKey);
		$this->assertFalse($metaColumns['user_binary']->primaryKey);
	}

	public function testGetColumnIsUniqueKey()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertFalse($metaColumns['user_id']->uniqueKey);
		$this->assertFalse($metaColumns['user_type']->uniqueKey);
		$this->assertTrue($metaColumns['user_key']->uniqueKey);
		$this->assertFalse($metaColumns['user_name']->uniqueKey);
		$this->assertFalse($metaColumns['user_decimal']->uniqueKey);
		$this->assertFalse($metaColumns['user_binary']->uniqueKey);
	}

	public function testGetColumnIsAutoIncrement()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertTrue($metaColumns['user_id']->autoIncrement);
		$this->assertFalse($metaColumns['user_type']->autoIncrement);
		$this->assertFalse($metaColumns['user_key']->autoIncrement);
		$this->assertFalse($metaColumns['user_name']->autoIncrement);
		$this->assertFalse($metaColumns['user_decimal']->autoIncrement);
		$this->assertFalse($metaColumns['user_binary']->autoIncrement);
	}

	public function testCouldNotGetColumnComment()
	{
		$metaDataProcessor = new SqliteMetaDataProcessor();
		$metaColumns = $metaDataProcessor->getMetaColumns($this->getDriver(), 'users');

		$this->assertNull($metaColumns['user_id']->comment);
		$this->assertNull($metaColumns['user_type']->comment);
		$this->assertNull($metaColumns['user_key']->comment);
		$this->assertNull($metaColumns['user_name']->comment);
		$this->assertNull($metaColumns['user_decimal']->comment);
		$this->assertNull($metaColumns['user_binary']->comment);
	}

}
