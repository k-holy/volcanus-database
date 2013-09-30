<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\Adapter\Mysql;

use Volcanus\Database\QueryBuilder\Adapter\Mysql\MysqlQueryBuilder;
use Volcanus\Database\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaDataProcessor\MysqlMetaDataProcessor;

/**
 * Test for MysqlQueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class MysqlQueryBuilderTest extends \PHPUnit_Framework_TestCase
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
		return $this->getMock('Volcanus\Database\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder',
			isset($method) ? array($method) : array(),
			array(new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor()))
		);
	}

	public function testParameterTypeOfText()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('text', $builder->parameterType('text'));
		$this->assertEquals('text', $builder->parameterType('char'));
		$this->assertEquals('text', $builder->parameterType('varchar'));
		$this->assertEquals('text', $builder->parameterType('tinytext'));
		$this->assertEquals('text', $builder->parameterType('longtext'));
		$this->assertEquals('text', $builder->parameterType('mediumtext'));
	}

	public function testParameterTypeOfInt()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('int', $builder->parameterType('int'));
		$this->assertEquals('int', $builder->parameterType('integer'));
		$this->assertEquals('int', $builder->parameterType('tinyint'));
		$this->assertEquals('int', $builder->parameterType('int4'));
		$this->assertEquals('int', $builder->parameterType('smallint'));
		$this->assertEquals('int', $builder->parameterType('mediumint'));
		$this->assertEquals('int', $builder->parameterType('bigint'));
	}

	public function testParameterTypeOfFloat()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('float', $builder->parameterType('real'));
		$this->assertEquals('float', $builder->parameterType('double'));
		$this->assertEquals('float', $builder->parameterType('float'));
	}

	public function testParameterTypeOfDate()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('date', $builder->parameterType('date'));
	}

	public function testParameterTypeOfTimestamp()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals('timestamp', $builder->parameterType('timestamp'));
		$this->assertEquals('timestamp', $builder->parameterType('datetime'));
	}

	public function testParameterTypeReturnFalseWhenUnsupportedType()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertFalse($builder->parameterType('unsupported-type'));
	}

	public function testParameterCallParameterBuilderToText()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toText');
		$parameterBuilder->expects($this->any())
			->method('toText')
			->will($this->returnValue('Foo'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals('Foo', $builder->parameter('Foo', 'text'));
	}

	public function testParameterCallParameterBuilderToInt()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('1'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals('1', $builder->parameter(1, 'int'));
	}

	public function testParameterCallParameterBuilderToFloat()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toFloat');
		$parameterBuilder->expects($this->any())
			->method('toFloat')
			->will($this->returnValue('0.1'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
	}

	public function testParameterCallParameterBuilderToBool()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toBool');
		$parameterBuilder->expects($this->any())
			->method('toBool')
			->will($this->returnValue('1'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals('1', $builder->parameter(true, 'bool'));
	}

	public function testParameterCallParameterBuilderToDate()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toDate');
		$parameterBuilder->expects($this->any())
			->method('toDate')
			->will($this->returnValue("STR_TO_DATE('2013-01-02', '%Y-%m-%d')"));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->parameter('2013-01-02', 'date'));
	}

	public function testParameterCallParameterBuilderToTimestamp()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toTimestamp');
		$parameterBuilder->expects($this->any())
			->method('toTimestamp')
			->will($this->returnValue("STR_TO_DATE('2013-01-02 00:00:00' '%Y-%m-%d %h:%i:%s')"));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals("STR_TO_DATE('2013-01-02 00:00:00' '%Y-%m-%d %h:%i:%s')", $builder->parameter('2013-01-02 00:00:00', 'timestamp'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testParameterRaiseExceptionWhenUnsupportedType()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$builder->parameter('Foo', 'unsupported-type');
	}

	public function testSelectLimit()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('20'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals(
			'SELECT * FROM test LIMIT 20,20',
			$builder->selectLimit('SELECT * FROM test', 20, 20)
		);
	}

	public function testSelectLimitWithoutOffset()
	{
		$parameterBuilder = $this->getParameterBuilderMock('toInt');
		$parameterBuilder->expects($this->any())
			->method('toInt')
			->will($this->returnValue('20'));

		$builder = new MysqlQueryBuilder($parameterBuilder);
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

		$builder = new MysqlQueryBuilder($parameterBuilder);
		$this->assertEquals(
			'SELECT * FROM test LIMIT 20,18446744073709551615',
			$builder->selectLimit('SELECT * FROM test', null, 20)
		);
	}

	public function testSelectCount()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals(
			'SELECT COUNT(*) FROM (SELECT * FROM test) AS X',
			$builder->selectCount('SELECT * FROM test')
		);
	}

	public function testSelectCountWithSqlCalcFoundRows()
	{
		$builder = new MysqlQueryBuilder($this->getParameterBuilderMock());
		$this->assertEquals(
			'SELECT FOUND_ROWS()',
			$builder->selectCount('SELECT SQL_CALC_FOUND_ROWS  * FROM test')
		);
	}

}
