<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder;

use Volcanus\Database\QueryBuilder\QueryBuilder;

use Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteQueryBuilder;
use Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteExpressionBuilder;
use Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaDataProcessor\SqliteMetaDataProcessor;

/**
 * Test for QueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	private function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
			static::$pdo->exec(<<<SQL
CREATE TABLE test(
     id         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
    ,name       TEXT
    ,updated_at DATETIME NOT NULL
);
SQL
			);
		}
		return static::$pdo;
	}

	private function getDriver()
	{
		return new PdoDriver(
			$this->getPdo(),
			new SqliteMetaDataProcessor()
		);
	}

	private function getBuilder()
	{
		return new SqliteQueryBuilder(
			new SqliteExpressionBuilder(),
			new SqliteParameterBuilder($this->getDriver())
		);
	}

	public function testExpressions()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$expressions = $builder->expressions('test');
		$this->assertEquals('test.id AS "id"', $expressions['id']);
		$this->assertEquals('test.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS \"updated_at\"", $expressions['updated_at']);
	}

	public function testExpressionsWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$expressions = $builder->expressions('test', 't01');
		$this->assertEquals('t01.id AS "id"', $expressions['id']);
		$this->assertEquals('t01.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', t01.updated_at) AS \"updated_at\"", $expressions['updated_at']);
	}

	public function testExpressionsWithExcludeKeys()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$expressions = $builder->expressions('test', null, array('updated_at'));
		$this->assertArrayHasKey('id', $expressions);
		$this->assertArrayHasKey('name', $expressions);
		$this->assertArrayNotHasKey('updated_at', $expressions);
	}

	public function testExpressionsWithColumnAliases()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$expressions = $builder->expressions('test', null, null, array('updated_at' => 'updated_at_formatted'));
		$this->assertEquals('test.id AS "id"', $expressions['id']);
		$this->assertEquals('test.name AS "name"', $expressions['name']);
		$this->assertEquals("strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS \"updated_at_formatted\"", $expressions['updated_at']);
	}

	public function testParameters()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$parameters = $builder->parameters('test', $columns);
		$this->assertEquals('1', $parameters['id']);
		$this->assertEquals("'Foo'", $parameters['name']);
		$this->assertEquals("datetime('2013-10-01 00:00:00')", $parameters['updated_at']);
	}

	public function testInsert()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
INSERT INTO
test
(id, name, updated_at)
VALUES
(1, 'Foo', datetime('2013-10-01 00:00:00'))
SQL
			, $builder->insert('test', $columns)
		);
	}

	public function testUpdate()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
UPDATE
test
SET
id = 1,
name = 'Foo',
updated_at = datetime('2013-10-01 00:00:00')
SQL
			, $builder->update('test', $columns)
		);
	}

	public function testUpdateWithWhere()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id'         => '1',
			'name'       => 'Foo',
			'updated_at' => new \DateTime('2013-10-01 00:00:00'),
		);
		$this->assertEquals(<<<SQL
UPDATE
test
SET
id = 1,
name = 'Foo',
updated_at = datetime('2013-10-01 00:00:00')
WHERE
id = 1
SQL
			, $builder->update('test', $columns, 'id = 1')
		);
	}

	public function testDelete()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
DELETE FROM
test
SQL
			, $builder->delete('test')
		);
	}

	public function testDeleteWithWhere()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
DELETE FROM
test
WHERE
id = 1
SQL
			, $builder->delete('test', 'id = 1')
		);
	}

	public function testSelectSyntax()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
SQL
			, $builder->selectSyntax('test')
		);
	}

	public function testFromSyntax()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
FROM
test
SQL
			, $builder->fromSyntax('test')
		);
	}

	public function testSelect()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
FROM
test
SQL
			, $builder->select('test')
		);
	}

	public function testSelectWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
t01.id AS "id",
t01.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', t01.updated_at) AS "updated_at"
FROM
test t01
SQL
			, $builder->select('test', 't01')
		);
	}

	public function testSelectWithWhere()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at"
FROM
test
WHERE
id = 1
SQL
			, $builder->select('test', null, 'id = 1')
		);
	}

	public function testSelectWithExcludeKeys()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name"
FROM
test
SQL
			, $builder->select('test', null, null, array('updated_at'))
		);
	}

	public function testSelectWithColumnAliases()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals(<<<SQL
SELECT
test.id AS "id",
test.name AS "name",
strftime('%Y-%m-%d %H:%i:%s', test.updated_at) AS "updated_at_formatted"
FROM
test
SQL
			, $builder->select('test', null, null, null, array('updated_at' => 'updated_at_formatted'))
		);
	}

	public function testLimitOffset()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$this->assertEquals("SELECT * FROM test LIMIT 10 OFFSET 10",
			$builder->limitOffset("SELECT * FROM test", 10, 10)
		);
	}

	public function testWhereExpressionsEqual()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => '1',
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id = 1', $expressions[0]);
	}

	public function testWhereExpressionsEqualWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => '1',
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id = 1', $expressions[0]);
	}

	public function testWhereExpressionsNotEqual()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => '1',
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id <> 1', $expressions[0]);
	}

	public function testWhereExpressionsNotEqualWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => '1',
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id <> 1', $expressions[0]);
	}

	public function testWhereExpressionsIn()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => array('1', '2', '3'),
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsInWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => array('1', '2', '3'),
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsNotIn()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => array('1', '2', '3'),
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id NOT IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsNotInWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => array('1', '2', '3'),
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id NOT IN (1,2,3)', $expressions[0]);
	}

	public function testWhereExpressionsIsNull()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => 'NULL'
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IS NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNullWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			'id' => 'NULL'
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IS NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNotNull()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => 'NULL'
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id IS NOT NULL', $expressions[0]);
	}

	public function testWhereExpressionsIsNotNullWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NEGATIVE . 'id' => 'NULL'
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id IS NOT NULL', $expressions[0]);
	}

	public function testWhereExpressionsNoConvert()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NO_CONVERT . 'id' => '= (SELECT id FROM test WHERE 1=1)'
		);
		$expressions = $builder->whereExpressions('test', null, $columns);
		$this->assertEquals('test.id = (SELECT id FROM test WHERE 1=1)', $expressions[0]);
	}

	public function testWhereExpressionsNoConvertWithTableAlias()
	{
		$builder = new QueryBuilder($this->getDriver(), $this->getBuilder());
		$columns = array(
			QueryBuilder::PREFIX_NO_CONVERT . 'id' => '= (SELECT id FROM test WHERE 1=1)'
		);
		$expressions = $builder->whereExpressions('test', 't01', $columns);
		$this->assertEquals('t01.id = (SELECT id FROM test WHERE 1=1)', $expressions[0]);
	}

}
