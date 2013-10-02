<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder;

use Volcanus\Database\QueryBuilder\QueryBuilder;
use Volcanus\Database\Driver\Pdo\PdoDriver;

/**
 * Test for QueryBuilder
 *
 * @author k.holy74@gmail.com
 */
class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{

	public function testCreateAdapterByFactory()
	{
		$this->assertInstanceOf('Volcanus\Database\QueryBuilder\Adapter\\Sqlite\\SqliteQueryBuilder',
			QueryBuilder::factory(
				new PdoDriver(
					new \PDO('sqlite::memory:')
				)
			)
		);
	}

	public function testCreateFacade()
	{
		$this->assertInstanceOf('Volcanus\Database\QueryBuilder\Facade',
			QueryBuilder::facade(
				new PdoDriver(
					new \PDO('sqlite::memory:')
				)
			)
		);
	}

}
