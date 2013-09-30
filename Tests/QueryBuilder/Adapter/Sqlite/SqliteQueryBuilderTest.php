<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\Adapter\Sqlite;

use Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteQueryBuilder;
use Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaDataProcessor\SqliteMetaDataProcessor;

/**
 * Test for SqliteQueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class SqliteQueryBuilderTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	public function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
		}
		return static::$pdo;
	}

	private function getParameterBuilderMock($method = null)
	{
		return $this->getMock('Volcanus\Database\QueryBuilder\Adapter\Sqlite\SqliteParameterBuilder',
			isset($method) ? array($method) : array(),
			array(new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor()))
		);
	}

	public function testParameterTypeOfText()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('text', $builder->parameterType('character'));
		$this->assertEquals('text', $builder->parameterType('varchar'));
		$this->assertEquals('text', $builder->parameterType('varying character'));
		$this->assertEquals('text', $builder->parameterType('nchar'));
		$this->assertEquals('text', $builder->parameterType('native character'));
		$this->assertEquals('text', $builder->parameterType('nvarchar'));
		$this->assertEquals('text', $builder->parameterType('text'));
		$this->assertEquals('text', $builder->parameterType('clob'));
	}

	public function testParameterTypeOfInt()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('int', $builder->parameterType('int'));
		$this->assertEquals('int', $builder->parameterType('integer'));
		$this->assertEquals('int', $builder->parameterType('tinyint'));
		$this->assertEquals('int', $builder->parameterType('smallint'));
		$this->assertEquals('int', $builder->parameterType('mediumint'));
		$this->assertEquals('int', $builder->parameterType('bigint'));
		$this->assertEquals('int', $builder->parameterType('int2'));
		$this->assertEquals('int', $builder->parameterType('int8'));
	}

	public function testParameterTypeOfFloat()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('float', $builder->parameterType('real'));
		$this->assertEquals('float', $builder->parameterType('double'));
		$this->assertEquals('float', $builder->parameterType('double precision'));
		$this->assertEquals('float', $builder->parameterType('float'));
	}

	public function testParameterTypeOfBool()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('bool', $builder->parameterType('boolean'));
	}

	public function testParameterTypeOfDate()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('date', $builder->parameterType('date'));
	}

	public function testParameterTypeOfTimestamp()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('timestamp', $builder->parameterType('datetime'));
	}

	public function testParameterTypeReturnFalseWhenUnsupportedType()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertFalse($builder->parameterType('unsupported-type'));
	}

	public function testParameterCallParameterBuilderToText()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toText');
		$parameterBuilder->expects($this->any())
			->method('toText')
			->will($this->returnValue('Foo'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals('Foo', $builder->parameter('Foo', 'text'));
	}

	public function testParameterCallParameterBuilderToInt()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('1'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals('1', $builder->parameter(1, 'int'));
	}

	public function testParameterCallParameterBuilderToFloat()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toFloat');
		$parameterBuilder->expects($this->any())
			->method('toFloat')
			->will($this->returnValue('0.1'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
	}

	public function testParameterCallParameterBuilderToBool()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toBool');
		$parameterBuilder->expects($this->any())
			->method('toBool')
			->will($this->returnValue('1'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals('1', $builder->parameter(true, 'bool'));
	}

	public function testParameterCallParameterBuilderToDate()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toDate');
		$parameterBuilder->expects($this->any())
			->method('toDate')
			->will($this->returnValue("TO_DATE('2013-01-02')"));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals("TO_DATE('2013-01-02')", $builder->parameter('2013-01-02', 'date'));
	}

	public function testParameterCallParameterBuilderToTimestamp()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toTimestamp');
		$parameterBuilder->expects($this->any())
			->method('toTimestamp')
			->will($this->returnValue("TO_TIMESTAMP('2013-01-02 00:00:00')"));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals("TO_TIMESTAMP('2013-01-02 00:00:00')", $builder->parameter('2013-01-02 00:00:00', 'timestamp'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testParameterRaiseExceptionWhenUnsupportedType()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$builder->parameter('Foo', 'unsupported-type');
	}

	public function testSelectLimit()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('20'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals(
			'SELECT * FROM test LIMIT 20 OFFSET 20',
			$builder->selectLimit('SELECT * FROM test', 20, 20)
		);
	}

	public function testSelectLimitWithoutOffset()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('20'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals(
			'SELECT * FROM test LIMIT 20',
			$builder->selectLimit('SELECT * FROM test', 20)
		);
	}

	public function testSelectLimitWithoutLimit()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('20'));

		$builder = new SqliteQueryBuilder($parameterBuilder);
		$this->assertEquals(
			'SELECT * FROM test LIMIT 18446744073709551615 OFFSET 20',
			$builder->selectLimit('SELECT * FROM test', null, 20)
		);
	}

	public function testSelectCount()
	{
		$builder = new SqliteQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals(
			'SELECT COUNT(*) FROM (SELECT * FROM test) AS X',
			$builder->selectCount('SELECT * FROM test')
		);
	}

}
