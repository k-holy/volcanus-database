<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\Dsn;

/**
 * Test for Dsn
 *
 * @author k.holy74@gmail.com
 */
class DsnTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateFromString()
    {
        $dsn = Dsn::createFromString('sqlite::memory:');
        $this->assertInstanceOf('\Volcanus\Database\Dsn', $dsn);
    }

    public function testToPdoSqlite()
    {
        $dsn = new Dsn([
            'driver' => 'sqlite',
            'database' => '/full/path/to/file.sqlite',
        ]);
        $this->assertEquals('sqlite:/full/path/to/file.sqlite', $dsn->toPdo());
    }

    public function testToPdoMysql()
    {
        $dsn = new Dsn([
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'port' => '3306',
            'database' => 'test',
            'options' => [
                'unix_socket' => '/tmp/mysql.sock',
                'charset' => 'utf8',
            ],
        ]);
        $this->assertEquals(
            'mysql:host=localhost;port=3306;dbname=test;unix_socket=/tmp/mysql.sock;charset=utf8',
            $dsn->toPdo()
        );
    }

    public function testToPdoPgsql()
    {
        $dsn = new Dsn([
            'driver' => 'pgsql',
            'hostname' => 'localhost',
            'port' => '5432',
            'database' => 'test',
            'username' => 'user',
            'password' => 'pass',
        ]);
        $this->assertEquals(
            'pgsql:host=localhost;port=5432;dbname=test;user=user;password=pass',
            $dsn->toPdo()
        );
    }

    public function testToPdoRaiseExceptionWhenUnsupportedDriver()
    {
        $this->expectException(\RuntimeException::class);
        $dsn = new Dsn([
            'driver' => 'unsupported_driver',
        ]);
        $dsn->toPdo();
    }

    public function testToPdoRaiseExceptionWhenUnsupportedOption()
    {
        $this->expectException(\RuntimeException::class);
        $dsn = new Dsn([
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'port' => '3306',
            'database' => 'test',
            'options' => [
                'unix_socket' => '/tmp/mysql.sock',
                'charset' => 'utf8',
                'unsupported_option' => 'foo',
            ],
        ]);
        $dsn->toPdo();
    }

}
