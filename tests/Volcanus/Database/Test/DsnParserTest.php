<?php
/**
 * Volcanus libraries for PHP 8.1~
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
        $this->assertEquals('driver', $attributes['driver']);
    }

    public function testParseDriverAndDatabaseForSqliteOnUnix()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:/full/path/to/file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals('sqlite', $attributes['driver']);
        $this->assertEquals('/full/path/to/file.sqlite', $attributes['database']);
    }

    public function testParseDriverAndDatabaseForSqliteOnWindows()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:c:\full\path\to\file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals('sqlite', $attributes['driver']);
        $this->assertEquals('c:\full\path\to\file.sqlite', $attributes['database']);
    }

    public function testParseDriverAndDatabaseForLegacySqliteOnUnix()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite:///full/path/to/file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals('sqlite', $attributes['driver']);
        $this->assertEquals('/full/path/to/file.sqlite', $attributes['database']);
    }

    public function testParseDriverAndDatabaseForLegacySqliteOnWindows()
    {
        $parser = new DsnParser();
        $parser->parseDriver('sqlite://c:\full\path\to\file.sqlite');
        $attributes = $parser->getAttributes();
        $this->assertEquals('sqlite', $attributes['driver']);
        $this->assertEquals('c:\full\path\to\file.sqlite', $attributes['database']);
    }

    public function testParseUsernameAndPassword()
    {
        $parser = new DsnParser();
        $parser->parseUsernameAndPassword('username:password@hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals('username', $attributes['username']);
        $this->assertEquals('password', $attributes['password']);
    }

    public function testParseUsername()
    {
        $parser = new DsnParser();
        $parser->parseUsernameAndPassword('username@hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals('username', $attributes['username']);
    }

    public function testParseHostnameAndPort()
    {
        $parser = new DsnParser();
        $parser->parseHostnameAndPort('hostname:port/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
    }

    public function testParseHostname()
    {
        $parser = new DsnParser();
        $parser->parseHostnameAndPort('hostname/database?option=value');
        $attributes = $parser->getAttributes();
        $this->assertEquals('hostname', $attributes['hostname']);
    }

    public function testParseDatabaseAndOptions()
    {
        $parser = new DsnParser();
        $parser->parseDatabaseAndOptions('database?opt1=val1&opt2=val2&opt3=val3');
        $attributes = $parser->getAttributes();
        $this->assertEquals('database', $attributes['database']);
        $this->assertEquals('val1', $attributes['options']['opt1']);
        $this->assertEquals('val2', $attributes['options']['opt2']);
        $this->assertEquals('val3', $attributes['options']['opt3']);
    }

    public function testParseDatabase()
    {
        $parser = new DsnParser();
        $parser->parseDatabaseAndOptions('database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('database', $attributes['database']);
    }

    public function testParseDriverAndUsernameAndPasswordAndHostnameAndPortAndDatabaseAndOptions()
    {
        $parser = new DsnParser('driver://username:password@hostname:port/database?opt1=val1&opt2=val2&opt3=val3');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertEquals('password', $attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertEquals('val1', $attributes['options']['opt1']);
        $this->assertEquals('val2', $attributes['options']['opt2']);
        $this->assertEquals('val3', $attributes['options']['opt3']);
    }

    public function testParseDriverAndUsernameAndPasswordAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://username:password@hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertEquals('password', $attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://username@hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndPasswordAndHostname()
    {
        $parser = new DsnParser('driver://username:password@hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertEquals('password', $attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostnameAndDatabase()
    {
        $parser = new DsnParser('driver://username@hostname/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndUsernameAndHostname()
    {
        $parser = new DsnParser('driver://username@hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertEquals('username', $attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndPortAndDatabase()
    {
        $parser = new DsnParser('driver://hostname:port/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndDatabase()
    {
        $parser = new DsnParser('driver://hostname/database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertEquals('database', $attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostnameAndPort()
    {
        $parser = new DsnParser('driver://hostname:port');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertEquals('port', $attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndHostname()
    {
        $parser = new DsnParser('driver://hostname');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertEquals('hostname', $attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertNull($attributes['database']);
        $this->assertNull($attributes['options']);
    }

    public function testParseDriverAndDatabase()
    {
        $parser = new DsnParser('driver:///database');
        $attributes = $parser->getAttributes();
        $this->assertEquals('driver', $attributes['driver']);
        $this->assertNull($attributes['username']);
        $this->assertNull($attributes['password']);
        $this->assertNull($attributes['hostname']);
        $this->assertNull($attributes['port']);
        $this->assertEquals('database', $attributes['database']);
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
        $this->assertEquals('driver:/@', $attributes['driver']);
        $this->assertEquals('username:/@', $attributes['username']);
        $this->assertEquals('password:/@', $attributes['password']);
        $this->assertEquals('hostname:/@', $attributes['hostname']);
        $this->assertEquals('port:/@', $attributes['port']);
        $this->assertEquals('database:/@', $attributes['database']);
        $this->assertEquals('val1:/@', $attributes['options']['opt1:/@']);
        $this->assertEquals('val2:/@', $attributes['options']['opt2:/@']);
        $this->assertEquals('val3:/@', $attributes['options']['opt3:/@']);
    }

    public function testParseRaiseInvalidArgumentExceptionWhenDriverNotFound()
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $parser = new DsnParser('username:password@hostname:port/database?option=value');
    }

}
