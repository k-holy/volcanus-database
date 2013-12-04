<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for PdoDriver
 *
 * @author k.holy74@gmail.com
 */
class PdoDriverTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	public function tearDown()
	{
		$this->getPdo()->exec("DELETE FROM test");
		$this->getPdo()->exec("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'test'");
	}

	public function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
			static::$pdo->exec(<<<SQL
CREATE TABLE test(
     id         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
    ,name       TEXT
);
SQL
			);
		}
		return static::$pdo;
	}

	public function testConnect()
	{
		$driver = new PdoDriver();
		$this->assertFalse($driver->connected());
		$driver->connect($this->getPdo());
		$this->assertTrue($driver->connected());
	}

	public function testDisconnect()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertTrue($driver->connected());
		$driver->disconnect();
		$this->assertFalse($driver->connected());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConnectRaiseExceptionWhenInvalidResource()
	{
		$driver = new PdoDriver();
		$driver->connect('foo');
	}

	public function testGetDriverName()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertEquals('sqlite', $driver->getDriverName());
	}

	public function testGetDriverNameReturnedNullAfterDisconnected()
	{
		$driver = new PdoDriver($this->getPdo());
		$driver->disconnect();
		$this->assertNull($driver->getDriverName());
	}

	public function testCreateMetaDataProcessor()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertInstanceOf('\Volcanus\Database\MetaData\SqliteMetaDataProcessor',
			$driver->createMetaDataProcessor()
		);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testCreateMetaDataProcessorRaiseExceptionWhenAfterDisconnected()
	{
		$driver = new PdoDriver($this->getPdo());
		$driver->disconnect();
		$driver->createMetaDataProcessor();
	}

	public function testPrepareReturnedPdoStatement()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertInstanceOf('\Volcanus\Database\Driver\Pdo\PdoStatement',
			$driver->prepare("SELECT id, name FROM test WHERE id = :id")
		);
	}

	public function testQueryReturnedPdoStatement()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertInstanceOf('\Volcanus\Database\Driver\Pdo\PdoStatement',
			$driver->query("SELECT count(*) FROM test")
		);
	}

	public function testExecuteReturnedAffectedRows()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertEquals(0, $driver->execute("SELECT count(*) FROM test"));
		$this->assertEquals(1, $driver->execute("INSERT INTO test (name) VALUES ('test')"));
		$this->assertEquals(1, $driver->execute("INSERT INTO test (name) VALUES ('test')"));
		$this->assertEquals(2, $driver->execute("UPDATE test SET name='retest' WHERE name = 'test'"));
	}

	public function testPrepareSetLastQuery()
	{
		$sql = "SELECT id, name FROM test WHERE id = :id";
		$driver = new PdoDriver($this->getPdo());
		$driver->prepare($sql);
		$this->assertEquals($sql, $driver->getLastQuery());
	}

	public function testQuerySetLastQuery()
	{
		$sql = "SELECT count(*) FROM test";
		$driver = new PdoDriver($this->getPdo());
		$driver->query($sql);
		$this->assertEquals($sql, $driver->getLastQuery());
	}

	public function testExecuteSetLastQuery()
	{
		$sql = "SELECT count(*) FROM test";
		$driver = new PdoDriver($this->getPdo());
		$driver->execute($sql);
		$this->assertEquals($sql, $driver->getLastQuery());
	}

	public function testGetLastError()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertNull($driver->getLastError());
		$driver->execute("SELECT count(*) FROM undefined_table");
		$this->assertNotNull($driver->getLastError());
	}

	public function testLastInsertId()
	{
		$driver = new PdoDriver($this->getPdo());
		$driver->execute("INSERT INTO test (name) VALUES ('test')");
		$this->assertEquals(1, $driver->lastInsertId());
		$driver->execute("INSERT INTO test (name) VALUES ('test')");
		$this->assertEquals(2, $driver->lastInsertId());
	}

	public function testGetMetaTables()
	{
		$driver = new PdoDriver($this->getPdo());
		$tables = $driver->getMetaTables();
		$this->assertArrayHasKey('test', $tables);
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Table', $tables['test']);
	}

	public function testGetMetaColumns()
	{
		$driver = new PdoDriver($this->getPdo());
		$columns = $driver->getMetaColumns('test');
		$this->assertArrayHasKey('id'  , $columns);
		$this->assertArrayHasKey('name', $columns);
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Column', $columns['id']);
		$this->assertInstanceOf('\Volcanus\Database\MetaData\Column', $columns['name']);
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetMetaTablesRaiseExceptionWhenMetaDataProcessorIsNotSet()
	{
		$driver = new PdoDriver();
		$driver->connect($this->getPdo());
		$driver->getMetaTables();
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testGetMetaColumnsRaiseExceptionWhenMetaDataProcessorIsNotSet()
	{
		$driver = new PdoDriver();
		$driver->connect($this->getPdo());
		$driver->getMetaColumns('test');
	}

	public function testQuote()
	{
		$driver = new PdoDriver($this->getPdo());
		$this->assertEquals("'Foo'", $driver->quote('Foo'));
		$this->assertEquals("'''Foo'''", $driver->quote("'Foo'"));
	}

}
