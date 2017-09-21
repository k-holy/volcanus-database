<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

use Volcanus\Database\Driver\Pdo\PdoFactory;
use Volcanus\Database\Dsn;

/**
 * Test for PdoFactory
 *
 * @author k.holy74@gmail.com
 */
class PdoFactoryTest extends \PHPUnit_Framework_TestCase
{

	public function testCreate()
	{
		$pdo = PdoFactory::create('sqlite::memory:');
		$this->assertInstanceOf('\\PDO', $pdo);
		$this->assertEquals('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
	}

	public function testCreateFromDsn()
	{
		$pdo = PdoFactory::createFromDsn(new Dsn(array(
			'driver'   => 'sqlite',
			'database' => ':memory:',
		)));
		$this->assertInstanceOf('\\PDO', $pdo);
		$this->assertEquals('sqlite', $pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
	}

	public function testCreateSetDefaultErrorMode()
	{
		$pdo = PdoFactory::create('sqlite::memory:');
		$this->assertEquals(\PDO::ERRMODE_EXCEPTION, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
	}

	public function testCreateWithErrorMode()
	{
		$pdo = PdoFactory::create('sqlite::memory:', null, null, array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
		));
		$this->assertEquals(\PDO::ERRMODE_WARNING, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
	}

	public function testCreateFromDsnWithErrorMode()
	{
		$pdo = PdoFactory::createFromDsn(new Dsn(array(
			'driver' => 'sqlite',
			'database' => ':memory:',
		)), array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING,
		));
		$this->assertEquals(\PDO::ERRMODE_WARNING, $pdo->getAttribute(\PDO::ATTR_ERRMODE));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testCreateRaiseExceptionWhenInvalidDsn()
	{
		$pdo = PdoFactory::create('unsupported-driver:');
	}

}
