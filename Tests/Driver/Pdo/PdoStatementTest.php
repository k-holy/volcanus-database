<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\Driver\Pdo;

use Volcanus\Database\Statement;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\Driver\Pdo\PdoStatement;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;

/**
 * Test for PdoStatement
 *
 * @author k.holy74@gmail.com
 */
class PdoStatementTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	public function tearDown()
	{
		$this->getPdo()->exec("DELETE FROM test");
		$this->getPdo()->exec("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'test'");
	}

	private function getPdo()
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

	public function testFetchByFetchAssoc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$item = $statement->fetch(Statement::FETCH_ASSOC);
		$this->assertArrayHasKey('cnt', $item);
		$this->assertEquals('1', $item['cnt']);
	}

	public function testFetchByFetchAssocReturnFalseWhenCannotContinue()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertFalse($statement->fetch(Statement::FETCH_ASSOC));
	}

	public function testFetchByFetchNum()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$item = $statement->fetch(Statement::FETCH_NUM);
		$this->assertArrayHasKey(0, $item);
		$this->assertEquals('1', $item[0]);
	}

	public function testFetchByFetchNumReturnFalseWhenCannotContinue()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertFalse($statement->fetch(Statement::FETCH_NUM));
	}

	public function testFetchByFetchClass()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data');
		$item = $statement->fetch(Statement::FETCH_CLASS);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('1', $item->cnt);
	}

	public function testFetchByFetchClassWithArguments()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data', array('One', 'Two', 'Three'));
		$item = $statement->fetch(Statement::FETCH_CLASS);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('1', $item->cnt);
		$this->assertEquals('One', $item->one);
		$this->assertEquals('Two', $item->two);
		$this->assertEquals('Three', $item->three);
	}

	public function testFetchByFetchClassReturnFalseWhenCannotContinue()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data');
		$this->assertFalse($statement->fetch(Statement::FETCH_CLASS));
	}

	public function testFetchByFetchFunc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name, 'Foo' AS foo, 'Bar' AS bar, 'Baz' AS baz FROM test WHERE id = 1")
		);
		$statement->setFetchMode(Statement::FETCH_FUNC, function($id, $name, $foo, $bar, $baz) {
			$item = new Data();
			$item->id = $id;
			$item->name = $name;
			$item->foo = $foo;
			$item->bar = $bar;
			$item->baz = $baz;
			return $item;
		});
		$item = $statement->fetch(Statement::FETCH_FUNC);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('1', $item->id);
		$this->assertEquals('test', $item->name);
		$this->assertEquals('Foo', $item->foo);
		$this->assertEquals('Bar', $item->bar);
		$this->assertEquals('Baz', $item->baz);
	}

	public function testFetchByFetchFuncReturnFalseWhenCannotContinue()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_FUNC, function($cnt) {
			$item = new Data();
			$item->cnt = $cnt;
			return $item;
		});
		$this->assertFalse($statement->fetch(Statement::FETCH_FUNC));
	}

	public function testFetchInstanceOf()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test WHERE id = 1")
		);
		$item = $statement->fetchInstanceOf(__NAMESPACE__ . '\\Data');
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('1', $item->id);
		$this->assertEquals('test', $item->name);
	}

	public function testFetchInstanceOfWithArguments()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test WHERE id = 1")
		);
		$item = $statement->fetchInstanceOf(__NAMESPACE__ . '\\Data', array('One', 'Two', 'Three'));
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('1', $item->id);
		$this->assertEquals('test', $item->name);
		$this->assertEquals('One', $item->one);
		$this->assertEquals('Two', $item->two);
		$this->assertEquals('Three', $item->three);
	}

	public function testFetchInstanceOfIgnoredUndefinedProperty()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name, 'Foo' AS foo, 'Bar' AS bar, 'Baz' AS baz FROM test WHERE id = 1")
		);
		$item = $statement->fetchInstanceOf(__NAMESPACE__ . '\\Data', null, true);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertObjectNotHasAttribute('foo', $item);
		$this->assertObjectNotHasAttribute('bar', $item);
		$this->assertObjectNotHasAttribute('baz', $item);
	}

	public function testFetchInstanceOfAcceptUndefinedProperty()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name, 'Foo' AS foo, 'Bar' AS bar, 'Baz' AS baz FROM test WHERE id = 1")
		);
		$item = $statement->fetchInstanceOf(__NAMESPACE__ . '\\Data', null, false);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
		$this->assertEquals('Foo', $item->foo);
		$this->assertEquals('Bar', $item->bar);
		$this->assertEquals('Baz', $item->baz);
	}

	public function testFetchInstanceOfReturnFalseWhenCannotContinue()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertFalse($statement->fetchInstanceOf(__NAMESPACE__ . '\\Data'));
	}

	public function testFetchByDefaultFetchMode()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_NUM);
		$item = $statement->fetch();
		$this->assertArrayHasKey(0, $item);
		$this->assertEquals('1', $item[0]);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFetchRaiseInvalidArgumentExceptionWhenUnsupportedFetchMode()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$item = $statement->fetch('Unsupported-FetchMode');
	}

	public function testFetchAllByFetchAssoc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_ASSOC);
		$this->assertCount(2, $items);
		$this->assertEquals('1'    , $items[0]['id']);
		$this->assertEquals('test1', $items[0]['name']);
		$this->assertEquals('2'    , $items[1]['id']);
		$this->assertEquals('test2', $items[1]['name']);
	}

	public function testFetchAllByFetchAssocReturnEmptyArray()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertEmpty($statement->fetchAll(Statement::FETCH_ASSOC));
	}

	public function testFetchAllByFetchNum()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_NUM);
		$this->assertCount(2, $items);
		$this->assertEquals('1'    , $items[0][0]);
		$this->assertEquals('test1', $items[0][1]);
		$this->assertEquals('2'    , $items[1][0]);
		$this->assertEquals('test2', $items[1][1]);
	}

	public function testFetchAllByFetchNumReturnEmptyArray()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertEmpty($statement->fetchAll(Statement::FETCH_NUM));
	}

	public function testFetchAllByFetchClass()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data');
		$this->assertCount(2, $items);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $items[0]);
		$this->assertEquals('1'    , $items[0]->id);
		$this->assertEquals('test1', $items[0]->name);
		$this->assertEquals('2'    , $items[1]->id);
		$this->assertEquals('test2', $items[1]->name);
	}

	public function testFetchAllByFetchClassWithArguments()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data', array('One', 'Two', 'Three'));
		$this->assertCount(2, $items);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $items[0]);
		$this->assertEquals('1'    , $items[0]->id);
		$this->assertEquals('test1', $items[0]->name);
		$this->assertEquals('One'  , $items[0]->one);
		$this->assertEquals('Two'  , $items[0]->two);
		$this->assertEquals('Three', $items[0]->three);
		$this->assertEquals('2'    , $items[1]->id);
		$this->assertEquals('test2', $items[1]->name);
		$this->assertEquals('One'  , $items[1]->one);
		$this->assertEquals('Two'  , $items[1]->two);
		$this->assertEquals('Three', $items[1]->three);
	}

	public function testFetchAllByFetchClassReturnEmptyArray()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertEmpty($statement->fetchAll(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFetchAllByFetchClassRaiseInvalidArgumentExceptionWhenUndefinedClass()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_CLASS, 'UndefinedClass');
	}

	public function testFetchAllByFetchFunc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name, 'Foo' AS foo, 'Bar' AS bar, 'Baz' AS baz FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_FUNC, function($id, $name, $foo, $bar, $baz) {
			$item = new Data();
			$item->id = $id;
			$item->name = $name;
			$item->foo = $foo;
			$item->bar = $bar;
			$item->baz = $baz;
			return $item;
		});
		$this->assertCount(2, $items);
		$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $items[0]);
		$this->assertEquals('1'    , $items[0]->id);
		$this->assertEquals('test1', $items[0]->name);
		$this->assertEquals('Foo'  , $items[0]->foo);
		$this->assertEquals('Bar'  , $items[0]->bar);
		$this->assertEquals('Baz'  , $items[0]->baz);
		$this->assertEquals('2'    , $items[1]->id);
		$this->assertEquals('test2', $items[1]->name);
		$this->assertEquals('Foo'  , $items[1]->foo);
		$this->assertEquals('Bar'  , $items[1]->bar);
		$this->assertEquals('Baz'  , $items[1]->baz);
	}

	public function testFetchAllByFetchFuncReturnEmptyArray()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$this->assertEmpty($statement->fetchAll(Statement::FETCH_FUNC, function($id, $name) {
			$item = new Data();
			$item->id = $id;
			$item->name = $name;
			return $item;
		}));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFetchAllByFetchFuncRaiseInvalidArgumentExceptionWhenInvalidType()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_FUNC, false);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFetchAllByFetchFuncRaiseInvalidArgumentExceptionWhenInvalidObject()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll(Statement::FETCH_FUNC, new \StdClass());
	}

	public function testFetchAllByDefaultFetchMode()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_ASSOC);
		$items = $statement->fetchAll();
		$this->assertCount(2, $items);
		$this->assertEquals('1'    , $items[0]['id']);
		$this->assertEquals('test1', $items[0]['name']);
		$this->assertEquals('2'    , $items[1]['id']);
		$this->assertEquals('test2', $items[1]['name']);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testFetchAllRaiseInvalidArgumentExceptionWhenUnsupportedFetchMode()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$items = $statement->fetchAll('Unsupported-FetchMode');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetFetchModeToFetchClassRaiseInvalidArgumentExceptionWhenUndefinedClass()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$item = $statement->setFetchMode(Statement::FETCH_CLASS, 'UndefinedClass');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetFetchModeToFetchFuncRaiseInvalidArgumentExceptionWhenInvalidType()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$item = $statement->setFetchMode(Statement::FETCH_FUNC, false);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetFetchModeToFetchFuncRaiseInvalidArgumentExceptionWhenInvalidObject()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$item = $statement->setFetchMode(Statement::FETCH_FUNC, new \StdClass());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetFetchModeRaiseInvalidArgumentExceptionWhenUnsupportedFetchMode()
	{
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT count(*) AS cnt FROM test")
		);
		$item = $statement->setFetchMode('Unsupported-FetchMode');
	}

	public function testExecutePreparedStatement()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->prepare("SELECT id, name FROM test WHERE id = :id")
		);
		$this->assertTrue($statement->execute(array('id' => 1)));
	}

	public function testExcutePreparedStatementThenFetch()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test')");
		$statement = new PdoStatement(
			$this->getPdo()->prepare("SELECT id, name FROM test WHERE id = :id")
		);
		$statement->execute(array('id' => 1));
		$item = $statement->fetch(Statement::FETCH_ASSOC);
		$this->assertArrayHasKey('id', $item);
		$this->assertArrayHasKey('name', $item);
		$this->assertEquals('1', $item['id']);
		$this->assertEquals('test', $item['name']);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExecuteRaiseInvalidArgumentExceptionWhenInvalidType()
	{
		$statement = new PdoStatement(
			$this->getPdo()->prepare("SELECT id, name FROM test WHERE id = :id")
		);
		$statement->execute(false);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testExecuteRaiseInvalidArgumentExceptionWhenInvalidObject()
	{
		$statement = new PdoStatement(
			$this->getPdo()->prepare("SELECT id, name FROM test WHERE id = :id")
		);
		$statement->execute(new \StdClass());
	}

	public function testExecuteRaiseRuntimeExceptionWhenCatchPDOExceptionAndExceptionMessageContainsDebugDumpParams()
	{
		$pdoStatement = $this->getMock('\\PDOStatement');
		$pdoStatement->expects($this->once())
			->method('setFetchMode')
			->will($this->returnValue(true));
		$pdoStatement->expects($this->once())
			->method('debugDumpParams')
			->will($this->returnCallback(function() {
				echo 'DEBUG_DUMP_PARAMS';
				return null;
			}));
		$pdoStatement->expects($this->once())
			->method('execute')
			->will($this->throwException(new \PDOException()));

		try {
			$statement = new PdoStatement($pdoStatement);
			$statement->execute(array('id' => 1));
		} catch (\RuntimeException $e) {
			$this->assertContains('DEBUG_DUMP_PARAMS', $e->getMessage());
		}
	}

	public function testIterationByFetchAssoc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_ASSOC);
		foreach ($statement as $item) {
			$this->assertArrayHasKey('id', $item);
			$this->assertArrayHasKey('name', $item);
			switch ($item['id']) {
			case '1':
				$this->assertEquals('test1', $item['name']);
				break;
			case '2':
				$this->assertEquals('test2', $item['name']);
				break;
			}
		}
	}

	public function testIterationByFetchNum()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_NUM);
		foreach ($statement as $item) {
			$this->assertArrayHasKey(0, $item);
			$this->assertArrayHasKey(1, $item);
			switch ($item[0]) {
			case '1':
				$this->assertEquals('test1', $item[1]);
				break;
			case '2':
				$this->assertEquals('test2', $item[1]);
				break;
			}
		}
	}

	public function testIterationByFetchClass()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data');
		foreach ($statement as $item) {
			$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
			switch ($item->id) {
			case '1':
				$this->assertEquals('test1', $item->name);
				break;
			case '2':
				$this->assertEquals('test2', $item->name);
				break;
			}
		}
	}

	public function testIterationByFetchClassWithArguments()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_CLASS, __NAMESPACE__ . '\\Data', array('One', 'Two', 'Three'));
		foreach ($statement as $item) {
			$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
			switch ($item->id) {
			case '1':
				$this->assertEquals('test1', $item->name);
				$this->assertEquals('One', $item->one);
				$this->assertEquals('Two', $item->two);
				$this->assertEquals('Three', $item->three);
				break;
			case '2':
				$this->assertEquals('test2', $item->name);
				$this->assertEquals('One', $item->one);
				$this->assertEquals('Two', $item->two);
				$this->assertEquals('Three', $item->three);
				break;
			}
		}
	}

	public function testIterationByFetchFunc()
	{
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test1')");
		$this->getPdo()->exec("INSERT INTO test (name) VALUES ('test2')");
		$statement = new PdoStatement(
			$this->getPdo()->query("SELECT id, name, 'Foo' AS foo, 'Bar' AS bar, 'Baz' AS baz FROM test")
		);
		$statement->setFetchMode(Statement::FETCH_FUNC, function($id, $name, $foo, $bar, $baz) {
			$item = new Data();
			$item->id = $id;
			$item->name = $name;
			$item->foo = $foo;
			$item->bar = $bar;
			$item->baz = $baz;
			return $item;
		});
		foreach ($statement as $item) {
			$this->assertInstanceOf(__NAMESPACE__ . '\\Data', $item);
			switch ($item->id) {
			case '1':
				$this->assertEquals('test1', $item->name);
				break;
			case '2':
				$this->assertEquals('test2', $item->name);
				break;
			}
			$this->assertEquals('Foo', $item->foo);
			$this->assertEquals('Bar', $item->bar);
			$this->assertEquals('Baz', $item->baz);
		}
	}

}

class Data
{
	public $id;
	public $name;
	public $cnt;
	public $one;
	public $two;
	public $three;
	private $attributes = array(
		'foo' => null,
		'bar' => null,
		'baz' => null,
	);

	public function __construct($one = null, $two = null, $three = null)
	{
		$this->one = $one;
		$this->two = $two;
		$this->three = $three;
	}

	public function __set($name, $value)
	{
		if (array_key_exists($name, $this->attributes)) {
			$this->attributes[$name] = $value;
		}
	}

	public function __get($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}
	}

	public function __isset($name)
	{
		return (array_key_exists($name, $this->attributes) && isset($this->attributes[$name]));
	}

	public function __unset($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			$this->attributes[$name] = null;
		}
	}

}
