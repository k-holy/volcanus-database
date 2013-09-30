<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder;

use Volcanus\Database\Tests\QueryBuilder\AbstractQueryBuilderTest\QueryBuilder;
use Volcanus\Database\Tests\QueryBuilder\AbstractParameterBuilderTest\ParameterBuilder;

/**
 * Test for AbstractParameter
 *
 * @author k.holy74@gmail.com
 */
class AbstractQueryBuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testParameterTypeOfText()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('text', $builder->parameterType('char'));
		$this->assertEquals('text', $builder->parameterType('varchar'));
		$this->assertEquals('text', $builder->parameterType('text'));
	}

	public function testParameterTypeOfInt()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('int', $builder->parameterType('int'));
		$this->assertEquals('int', $builder->parameterType('integer'));
	}

	public function testParameterTypeOfFloat()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('float', $builder->parameterType('float'));
		$this->assertEquals('float', $builder->parameterType('real'));
	}

	public function testParameterTypeOfBool()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('bool', $builder->parameterType('bool'));
		$this->assertEquals('bool', $builder->parameterType('boolean'));
	}

	public function testParameterTypeOfDate()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('date', $builder->parameterType('date'));
	}

	public function testParameterTypeOfTimestamp()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('timestamp', $builder->parameterType('timestamp'));
		$this->assertEquals('timestamp', $builder->parameterType('datetime'));
	}

	public function testParameterTypeReturnFalseWhenUnsupportedType()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertFalse($builder->parameterType('unsupported-type'));
	}

	public function testParameterToText()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals("'Foo'", $builder->parameter('Foo', 'text'));
	}

	public function testParameterToInt()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('1', $builder->parameter(1, 'int'));
	}

	public function testParameterToFloat()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
	}

	public function testParameterToBool()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals('1', $builder->parameter(true, 'bool'));
	}

	public function testParameterToDate()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals("TO_DATE('2013-01-02')", $builder->parameter('2013-01-02', 'date'));
	}

	public function testParameterToTimestamp()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals("TO_TIMESTAMP('2013-01-02 00:00:00')", $builder->parameter('2013-01-02 00:00:00', 'timestamp'));
	}

	/**
	 * @expectedException \RuntimeException
	 */
	public function testParameterRaiseExceptionWhenUnsupportedType()
	{
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$builder->parameter('Foo', 'unsupported-type');
	}

	public function testSelectLimit()
	{
		$sql = 'SELECT * FROM test';
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals(
			"SELECT * FROM test LIMIT 20 OFFSET 10",
			$builder->selectLimit($sql, 20, 10)
		);
	}

	public function testSelectCount()
	{
		$sql = 'SELECT * FROM test';
		$builder = new QueryBuilder(
			new ParameterBuilder()
		);
		$this->assertEquals(
			"SELECT COUNT(*) FROM (SELECT * FROM test) AS X",
			$builder->selectCount($sql)
		);
	}

}
