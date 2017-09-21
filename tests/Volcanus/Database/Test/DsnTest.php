<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\Dsn;

/**
 * Test for Dsn
 *
 * @author k.holy74@gmail.com
 */
class DsnTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateFromString()
    {
        $dsn = Dsn::createFromString('sqlite::memory:');
        $this->assertInstanceOf('\Volcanus\Database\Dsn', $dsn);
    }

    public function testToPdoSqlite()
    {
        $dsn = new Dsn(array(
            'driver' => 'sqlite',
            'database' => '/full/path/to/file.sqlite',
        ));
        $this->assertEquals('sqlite:/full/path/to/file.sqlite', $dsn->toPdo());
    }

    public function testToPdoMysql()
    {
        $dsn = new Dsn(array(
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'port' => '3306',
            'database' => 'test',
            'options' => array(
                'unix_socket' => '/tmp/mysql.sock',
                'charset' => 'utf8',
            ),
        ));
        $this->assertEquals(
            'mysql:host=localhost;port=3306;dbname=test;unix_socket=/tmp/mysql.sock;charset=utf8',
            $dsn->toPdo()
        );
    }

    public function testToPdoPgsql()
    {
        $dsn = new Dsn(array(
            'driver' => 'pgsql',
            'hostname' => 'localhost',
            'port' => '5432',
            'database' => 'test',
            'username' => 'user',
            'password' => 'pass',
        ));
        $this->assertEquals(
            'pgsql:host=localhost;port=5432;dbname=test;user=user;password=pass',
            $dsn->toPdo()
        );
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToPdoRaiseExceptionWhenUnsupportedDriver()
    {
        $dsn = new Dsn(array(
            'driver' => 'unsupported_driver',
        ));
        $dsn->toPdo();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToPdoRaiseExceptionWhenUnsupportedOption()
    {
        $dsn = new Dsn(array(
            'driver' => 'mysql',
            'hostname' => 'localhost',
            'port' => '3306',
            'database' => 'test',
            'options' => array(
                'unix_socket' => '/tmp/mysql.sock',
                'charset' => 'utf8',
                'unsupported_option' => 'foo',
            ),
        ));
        $dsn->toPdo();
    }

}
