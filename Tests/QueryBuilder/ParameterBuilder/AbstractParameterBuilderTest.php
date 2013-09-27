<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\ParameterBuilder;

use Volcanus\Database\QueryBuilder\ParameterBuilder\AbstractParameterBuilder;
use Volcanus\Database\QueryBuilder\ParameterBuilder\ParameterBuilderInterface;

/**
 * Test for AbstractParameter
 *
 * @author k.holy74@gmail.com
 */
class AbstractParameterBuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testParameterTypeOfText()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('text', $builder->parameterType('char'));
		$this->assertEquals('text', $builder->parameterType('varchar'));
		$this->assertEquals('text', $builder->parameterType('text'));
	}

	public function testParameterTypeOfInt()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('int', $builder->parameterType('int'));
		$this->assertEquals('int', $builder->parameterType('integer'));
	}

	public function testParameterTypeOfFloat()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('float', $builder->parameterType('float'));
		$this->assertEquals('float', $builder->parameterType('real'));
	}

	public function testParameterTypeOfDate()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('date', $builder->parameterType('date'));
	}

	public function testParameterTypeOfTimestamp()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('timestamp', $builder->parameterType('timestamp'));
		$this->assertEquals('timestamp', $builder->parameterType('datetime'));
	}

	public function testParameterToText()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('Foo', $builder->parameter('Foo', 'text'));
	}

	public function testParameterToInt()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('1', $builder->parameter(1, 'int'));
	}

	public function testParameterToFloat()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals('0.1', $builder->parameter(0.1, 'float'));
	}

	public function testParameterToDate()
	{
		$builder = new TestParameterBuilder();
		$this->assertEquals("TO_DATE('2013-01-02')", $builder->parameter('2013-01-02', 'date'));
	}

}

class TestParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
{
	protected static $types = array(
		'text'      => array('char', 'varchar','text'),
		'int'       => array('int', 'integer'),
		'float'     => array('float', 'real'),
		'date'      => array('date'),
		'timestamp' => array('timestamp', 'datetime'),
	);

	public function toText($value)
	{
		return sprintf('%s', $value);
	}

	public function toInt($value, $type = null)
	{
		return sprintf('%d', $value);
	}

	public function toFloat($value, $type = null)
	{
		return (string)$value;
	}

	public function toDate($value)
	{
		return sprintf("TO_DATE('%s')", $value);
	}

	public function toTimestamp($value)
	{
		return sprintf("TO_TIMESTAMP('%s')", $value);
	}

}
