<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\DsnParser;

/**
 * Test for DsnParser
 *
 * @author k.holy74@gmail.com
 */
class DsnParserTest extends \PHPUnit\Framework\TestCase
{

    public function testParseDriver()
    {
        $parser = new DsnParser();
        $parser->parseDriver('driver://username:password@hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
    }

    public function testParseDriverAndDatabaseForSqliteOnUnix()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:/full/path/to/file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'sqlite');
        $this->assertEquals($attributes['database'], '/full/path/to/file.sqlite');
    }

    public function testParseDriverAndDatabaseForSqliteOnWindows()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:c:\full\path\to\file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'sqlite');
        $this->assertEquals($attributes['database'], 'c:\full\path\to\file.sqlite');
    }

    public function testParseDriverAndDatabaseForLegacySqliteOnUnix()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:///full/path/to/file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'sqlite');
        $this->assertEquals($attributes['database'], '/full/path/to/file.sqlite');
    }

    public function testParseDriverAndDatabaseForLegacySqliteOnWindows()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite://c:\full\path\to\file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'sqlite');
        $this->assertEquals($attributes['database'], 'c:\full\path\to\file.sqlite');
    }

    public function testParseUsernameAndPassword()
    {
        $parser = new DsnParser();
        $parser->parseUsernameAndPassword('username:password@hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['username'], 'username');
        $this->assertEquals($attributes['password'], 'password');
    }

    public function testParseUsername()
    {
        $parser = new DsnParser();
        $parser->parseUsernameAndPassword('username@hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['username'], 'username');
    }

    public function testParseHostnameAndPort()
    {
        $parser = new DsnParser();
        $parser->parseHostnameAndPort('hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
    }

    public function testParseHostname()
    {
        $parser = new DsnParser();
        $parser->parseHostnameAndPort('hostname/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['hostname'], 'hostname');
    }

    public function testParseDatabaseAndOptions()
    {
        $parser = new DsnParser();
        $parser->parseDatabaseAndOptions('database?opt1=val1&opt2=val2&opt3=val3');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['database'], 'database');
        $this->assertEquals($attributes['options']['opt1'], 'val1');
        $this->assertEquals($attributes['options']['opt2'], 'val2');
        $this->assertEquals($attributes['options']['opt3'], 'val3');
    }

    public function testParseDatabase()
    {
        $parser = new DsnParser();
        $parser->parseDatabaseAndOptions('database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['database'], 'database');
    }

    public function testParseDriverAndUsernameAndPasswordAndHostnameAndPortAndDatabaseAndOptions()
    {
        $parser = new DsnParser('driver://username:password@hostname:port/database?opt1=val1&opt2=val2&opt3=val3');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertEquals($attributes['password'], 'password');
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
        $this->assertEquals($attributes['database'], 'database');
        $this->assertEquals($attributes['options']['opt1'], 'val1');
        $this->assertEquals($attributes['options']['opt2'], 'val2');
        $this->assertEquals($attributes['options']['opt3'], 'val3');
    }

    public function testParseDriverAndUsernameAndPasswordAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://username:password@hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertEquals($attributes['password'], 'password');
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://username@hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndPasswordAndHostname()
    {
        $parser = new DsnParser('driver://username:password@hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertEquals($attributes['password'], 'password');
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostnameAndDatabase()
    {
        $parser = new DsnParser('driver://username@hostname/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertNull($attributes['port']);
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostname()
    {
        $parser = new DsnParser('driver://username@hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertEquals($attributes['username'], 'username');
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndDatabase()
    {
        $parser = new DsnParser('driver://hostname/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertNull($attributes['port']);
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndPort()
    {
        $parser = new DsnParser('driver://hostname:port');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertEquals($attributes['port'], 'port');
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostname()
    {
        $parser = new DsnParser('driver://hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals($attributes['hostname'], 'hostname');
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndDatabase()
    {
        $parser = new DsnParser('driver:///database');
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver');
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertNull($attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertEquals($attributes['database'], 'database');
        $this->assertNull($attributes['options']);
    }

    public function testParseUrlencoded()
    {
        $parser = new DsnParser(sprintf('%s://%s:%s@%s:%s/%s?%s=%s&%s=%s&%s=%s',
            rawurlencode('driver:/@'),
            rawurlencode('username:/@'),
            rawurlencode('password:/@'),
            rawurlencode('hostname:/@'),
            rawurlencode('port:/@'),
            rawurlencode('database:/@'),
            rawurlencode('opt1:/@'),
            rawurlencode('val1:/@'),
            rawurlencode('opt2:/@'),
            rawurlencode('val2:/@'),
            rawurlencode('opt3:/@'),
            rawurlencode('val3:/@')
        ));
        $attributes = $parser->getAttributes();
        $this->assertEquals($attributes['driver'], 'driver:/@');
        $this->assertEquals($attributes['username'], 'username:/@');
        $this->assertEquals($attributes['password'], 'password:/@');
        $this->assertEquals($attributes['hostname'], 'hostname:/@');
        $this->assertEquals($attributes['port'], 'port:/@');
        $this->assertEquals($attributes['database'], 'database:/@');
        $this->assertEquals($attributes['options']['opt1:/@'], 'val1:/@');
        $this->assertEquals($attributes['options']['opt2:/@'], 'val2:/@');
        $this->assertEquals($attributes['options']['opt3:/@'], 'val3:/@');
    }

    public function testParseRaiseInvalidArgumentExceptionWhenDriverNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $parser = new DsnParser('username:password@hostname:port/database?option=value');
    }

}
