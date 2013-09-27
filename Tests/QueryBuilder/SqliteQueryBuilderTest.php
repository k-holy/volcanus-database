<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests;

use Volcanus\Database\QueryBuilder\SqliteQueryBuilder;
use Volcanus\Database\QueryBuilder\ParameterBuilder\SqliteParameterBuilder;
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

	public function testSelectLimit()
	{
		$sql = 'SELECT * FROM test';
		$builder = new SqliteQueryBuilder(
			new SqliteParameterBuilder(
				new PdoDriver($this->getPdo(), new SqliteMetaDataProcessor())
			)
		);
		$this->assertEquals(
			sprintf('%s LIMIT 20 OFFSET 0', $sql),
			$builder->selectLimit($sql, 20, 0)
		);
	}

}
