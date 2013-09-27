<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\ParameterBuilder;

use Volcanus\Database\QueryBuilder\ParameterBuilder\SqliteParameterBuilder;
use Volcanus\Database\QueryBuilder\ParameterBuilder\ParameterBuilderInterface;
use Volcanus\Database\QueryBuilder\QueryBuilder;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaDataProcessor\SqliteMetaDataProcessor;

/**
 * Test for SqliteParameter
 *
 * @author k.holy74@gmail.com
 */
class SqliteParameterBuilderTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	public function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
		}
		return static::$pdo;
	}

	public function testParameterTypeOfText()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('text', $builder->parameterType('char'));
		$this->assertEquals('text', $builder->parameterType('varchar'));
		$this->assertEquals('text', $builder->parameterType('character'));
		$this->assertEquals('text', $builder->parameterType('varying character'));
		$this->assertEquals('text', $builder->parameterType('nchar'));
		$this->assertEquals('text', $builder->parameterType('native character'));
		$this->assertEquals('text', $builder->parameterType('nvarchar'));
		$this->assertEquals('text', $builder->parameterType('text'));
		$this->assertEquals('text', $builder->parameterType('clob'));
	}

	public function testParameterTypeOfInt()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('int', $builder->parameterType('int'));
		$this->assertEquals('int', $builder->parameterType('integer'));
		$this->assertEquals('int', $builder->parameterType('tinyint'));
		$this->assertEquals('int', $builder->parameterType('smallint'));
		$this->assertEquals('int', $builder->parameterType('mediumint'));
		$this->assertEquals('int', $builder->parameterType('bigint'));
		$this->assertEquals('int', $builder->parameterType('int2'));
		$this->assertEquals('int', $builder->parameterType('int4'));
		$this->assertEquals('int', $builder->parameterType('int8'));
	}

	public function testParameterTypeOfFloat()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('float', $builder->parameterType('real'));
		$this->assertEquals('float', $builder->parameterType('double'));
		$this->assertEquals('float', $builder->parameterType('float'));
	}

	public function testParameterTypeOfDate()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('date', $builder->parameterType('date'));
	}

	public function testParameterTypeOfTimestamp()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('timestamp', $builder->parameterType('timestamp'));
		$this->assertEquals('timestamp', $builder->parameterType('datetime'));
	}

	public function testToText()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("'Foo'", $builder->toText('Foo'));
		$this->assertEquals("'''Foo'''", $builder->toText("'Foo'"));
	}

	public function testToTextNull()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toText(null));
	}

	public function testToTextEmptyString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toText(''));
	}

	public function testToInt()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toInt(1));
	}

	public function testToIntMin()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('-2147483648', $builder->toInt(QueryBuilder::MIN));
	}

	public function testToIntMax()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('2147483647', $builder->toInt(QueryBuilder::MAX));
	}

	public function testToIntNull()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toInt(null));
	}

	public function testToIntEmptyString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toInt(''));
	}

	public function testToFloat()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toFloat(1));
	}

	public function testToFloatMin()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('-9223372036854775808', $builder->toFloat(QueryBuilder::MIN));
	}

	public function testToFloatMax()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('9223372036854775807', $builder->toFloat(QueryBuilder::MAX));
	}

	public function testToFloatNull()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toFloat(null));
	}

	public function testToFloatEmptyString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toFloat(''));
	}

	public function testToDateString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('2013-01-02')", $builder->ToDate('2013-01-02'));
	}

	public function testToDateArrayOfString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('2013-01-02')", $builder->ToDate(array('2013','01', '02')));
		$this->assertEquals("date('2013-01-02')", $builder->ToDate(array('2013','1', '2')));
	}

	public function testToDateForArrayOfInt()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('2013-01-02')", $builder->ToDate(array(2013, 1, 2)));
	}

	public function testToDateMin()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('0000-01-01')", $builder->ToDate(QueryBuilder::MIN));
	}

	public function testToDateMax()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('9999-12-31')", $builder->ToDate(QueryBuilder::MAX));
	}

	public function testToDateNow()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("date('now')", $builder->ToDate(QueryBuilder::NOW));
	}

	public function testToDateNull()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->ToDate(null));
	}

	public function testToDateEmptyString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->ToDate(''));
	}

	public function testToTimestampString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp('2013-01-02 03:04:05'));
	}

	public function testToTimestampArrayOfString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(array('2013', '01', '02', '03', '04', '05')));
		$this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(array('2013', '1', '2', '3', '4', '5')));
	}

	public function testToTimestampArrayOfInt()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('2013-01-02 03:04:05')", $builder->toTimestamp(array(2013, 1, 2, 3, 4, 5)));
	}

	public function testToTimestampMin()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('0000-01-01 00:00:00')", $builder->toTimestamp(QueryBuilder::MIN));
	}

	public function testToTimestampMax()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('9999-12-31 23:59:59')", $builder->toTimestamp(QueryBuilder::MAX));
	}

	public function testToTimestampNow()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals("datetime('now')", $builder->toTimestamp(QueryBuilder::NOW));
	}

	public function testToTimestampNull()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTimestamp(null));
	}

	public function testToTimestampEmptyString()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTimestamp(''));
	}

	public function testToInt2()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toInt2(1));
	}

	public function testToInt2Min()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('-32768', $builder->toInt2(QueryBuilder::MIN));
	}

	public function testToInt2Max()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('32767', $builder->toInt2(QueryBuilder::MAX));
	}

	public function testToInt8()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toInt8(1));
	}

	public function testToInt8Min()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('-9223372036854775808', $builder->toInt8(QueryBuilder::MIN));
	}

	public function testToInt8Max()
	{
		$builder = new SqliteParameterBuilder(
			new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
		);
		$this->assertEquals('9223372036854775807', $builder->toInt8(QueryBuilder::MAX));
	}

}
