<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\Adapter\Mysql;

use Volcanus\Database\QueryBuilder\QueryBuilder;
use Volcanus\Database\QueryBuilder\Adapter\Mysql\MysqlParameterBuilder;

use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\MetaData\MysqlMetaDataProcessor;

/**
 * Test for MysqlParameter
 *
 * @author k.holy74@gmail.com
 */
class MysqlParameterBuilderTest extends \PHPUnit_Framework_TestCase
{

	private static $pdo;

	public function getPdo()
	{
		if (!isset(static::$pdo)) {
			static::$pdo = new \PDO('sqlite::memory:');
		}
		return static::$pdo;
	}

	public function testToText()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("'Foo'", $builder->toText('Foo'));
		$this->assertEquals("'''Foo'''", $builder->toText("'Foo'"));
	}

	public function testToTextNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toText(null));
	}

	public function testToTextEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toText(''));
	}

	public function testToInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toInt(1));
	}

	public function testToIntMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('-2147483648', $builder->toInt(QueryBuilder::MIN));
	}

	public function testToIntMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('2147483647', $builder->toInt(QueryBuilder::MAX));
	}

	public function testToIntNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toInt(null));
	}

	public function testToIntEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toInt(''));
	}

	public function testToTinyInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toTinyInt(1));
	}

	public function testToTinyIntMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('-128', $builder->toTinyInt(QueryBuilder::MIN));
	}

	public function testToTinyIntMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('127', $builder->toTinyInt(QueryBuilder::MAX));
	}

	public function testToTinyIntNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTinyInt(null));
	}

	public function testToTinyIntEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTinyInt(''));
	}

	public function testToSmallInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toSmallInt(1));
	}

	public function testToSmallIntMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('-32768', $builder->toSmallInt(QueryBuilder::MIN));
	}

	public function testToSmallIntMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('32767', $builder->toSmallInt(QueryBuilder::MAX));
	}

	public function testToSmallIntNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toSmallInt(null));
	}

	public function testToSmallIntEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toSmallInt(''));
	}

	public function testToMediumInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toMediumInt(1));
	}

	public function testToMediumIntMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('-8388608', $builder->toMediumInt(QueryBuilder::MIN));
	}

	public function testToMediumIntMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('8388607', $builder->toMediumInt(QueryBuilder::MAX));
	}

	public function testToMediumIntNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toMediumInt(null));
	}

	public function testToMediumIntEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toMediumInt(''));
	}

	public function testToBigInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toBigInt(1));
	}

	public function testToBigIntMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('-9223372036854775808', $builder->toBigInt(QueryBuilder::MIN));
	}

	public function testToBigIntMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('9223372036854775807', $builder->toBigInt(QueryBuilder::MAX));
	}

	public function testToBigIntNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toBigInt(null));
	}

	public function testToBigIntEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toBigInt(''));
	}

	public function testToFloat()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toFloat(1));
	}

	public function testToFloatMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("'-3.402823466E+38'", $builder->toFloat(QueryBuilder::MIN));
	}

	public function testToFloatMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("'3.402823466E+38'", $builder->toFloat(QueryBuilder::MAX));
	}

	public function testToFloatNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toFloat(null));
	}

	public function testToFloatEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toFloat(''));
	}

	public function testToDateString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate('2013-01-02'));
	}

	public function testToDateArrayOfString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate(array('2013','01', '02')));
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate(array('2013','1', '2')));
	}

	public function testToDateForArrayOfInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate(array(2013, 1, 2)));
	}

	public function testToDateForDateTime()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate(new \DateTime('2013-01-02')));
	}

	public function testToDateForUnixTimestamp()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02', '%Y-%m-%d')", $builder->ToDate(mktime(0, 0, 0, 1, 2, 2013)));
	}

	public function testToDateMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('1000-01-01', '%Y-%m-%d')", $builder->ToDate(QueryBuilder::MIN));
	}

	public function testToDateMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('9999-12-31', '%Y-%m-%d')", $builder->ToDate(QueryBuilder::MAX));
	}

	public function testToDateNow()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("CURDATE()", $builder->ToDate(QueryBuilder::NOW));
	}

	public function testToDateNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->ToDate(null));
	}

	public function testToDateEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->ToDate(''));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testToDateRaiseExceptionWhenInvalidValue()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$builder->ToDate(new \stdClass());
	}

	public function testToTimestampString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp('2013-01-02 03:04:05'));
	}

	public function testToTimestampArrayOfString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(array('2013', '01', '02', '03', '04', '05')));
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(array('2013', '1', '2', '3', '4', '5')));
	}

	public function testToTimestampArrayOfInt()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(array(2013, 1, 2, 3, 4, 5)));
	}

	public function testToTimestampForDateTime()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(new \DateTime('2013-01-02 03:04:05')));
	}

	public function testToTimestampForUnixTimestamp()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('2013-01-02 03:04:05', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(mktime(3, 4, 5, 1, 2, 2013)));
	}

	public function testToTimestampMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('1000-01-01 00:00:00', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(QueryBuilder::MIN));
	}

	public function testToTimestampMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("STR_TO_DATE('9999-12-31 23:59:59', '%Y-%m-%d %H:%i:%s')", $builder->toTimestamp(QueryBuilder::MAX));
	}

	public function testToTimestampNow()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals("NOW()", $builder->toTimestamp(QueryBuilder::NOW));
	}

	public function testToTimestampNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTimestamp(null));
	}

	public function testToTimestampEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toTimestamp(''));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testToTimestampRaiseExceptionWhenInvalidValue()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$builder->toTimestamp(new \stdClass());
	}

	public function testToBool()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toBool(1));
		$this->assertEquals('0', $builder->toBool(0));
		$this->assertEquals('1', $builder->toBool(true));
		$this->assertEquals('0', $builder->toBool(false));
	}

	public function testToBoolMin()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('0', $builder->toBool(QueryBuilder::MIN));
	}

	public function testToBoolMax()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('1', $builder->toBool(QueryBuilder::MAX));
	}

	public function testToBoolNull()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toBool(null));
	}

	public function testToBoolEmptyString()
	{
		$builder = new MysqlParameterBuilder(
			new PdoDriver($this->getPdo(), new MysqlMetaDataProcessor())
		);
		$this->assertEquals('NULL', $builder->toBool(''));
	}

}
