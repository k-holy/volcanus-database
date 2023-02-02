<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

use Volcanus\Database\Dsn;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\Driver\Pdo\PdoStatement;
use Volcanus\Database\Statement;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\MetaData\SqliteMetaDataProcessor;
use Volcanus\Database\MetaData\Table;

/**
 * Test for PdoDriver
 *
 * @author k.holy74@gmail.com
 */
class PdoDriverTest extends \PHPUnit\Framework\TestCase
{

    /** @var \PDO */
    private static \PDO $pdo;

    public function tearDown(): void
    {
        $this->getPdo()->exec("DELETE FROM test");
        $this->getPdo()->exec("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'test'");
    }

    public function getPdo(): \PDO
    {
        if (!isset(static::$pdo)) {
            $pdo = new \PDO('sqlite::memory:');
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
            static::$pdo = $pdo;
            static::$pdo->exec(<<<'SQL'
CREATE TABLE test(
  id    INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT 
, name  TEXT
);
SQL
            );
        }
        return static::$pdo;
    }

    public function testCreateFromDsn()
    {
        $driver = PdoDriver::createFromDsn(new Dsn([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]));
        $this->assertInstanceOf(PdoDriver::class, $driver);
    }

    public function testConnect()
    {
        $driver = new PdoDriver();
        $this->assertFalse($driver->connected());
        $driver->connect(new Dsn([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]));
        $this->assertTrue($driver->connected());
    }

    public function testDisconnect()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertTrue($driver->connected());
        $driver->disconnect();
        $this->assertFalse($driver->connected());
    }

    public function testGetDriverName()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertEquals('sqlite', $driver->getDriverName());
    }

    public function testGetDriverNameReturnedNullAfterDisconnected()
    {
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $this->assertNull($driver->getDriverName());
    }

    public function testCreateMetaDataProcessor()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertInstanceOf(SqliteMetaDataProcessor::class,
            $driver->createMetaDataProcessor()
        );
    }

    public function testCreateMetaDataProcessorRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $driver->createMetaDataProcessor();
    }

    public function testPrepareReturnedPdoStatement()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertInstanceOf(PdoStatement::class,
            $driver->prepare("SELECT id, name FROM test WHERE id = :id")
        );
    }

    public function testPrepareRaiseExceptionWhenInvalidQuery()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->prepare("SELECT id, name FROM undefined_table WHERE id = :id");
    }

    public function testPrepareRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $driver->prepare("SELECT id, name FROM test WHERE id = :id");
    }

    public function testQueryReturnedPdoStatement()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertInstanceOf(PdoStatement::class,
            $driver->query("SELECT count(*) FROM test")
        );
    }

    public function testQueryRaiseExceptionWhenInvalidQuery()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->query("SELECT * FROM undefined_table_called_by_testQueryRaiseExceptionWhenInvalidQuery");
    }

    public function testQueryRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $driver->query("SELECT count(*) FROM test");
    }

    public function testExecuteReturnedAffectedRows()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertEquals(0, $driver->execute("SELECT count(*) FROM test"));
        $this->assertEquals(1, $driver->execute("INSERT INTO test (name) VALUES ('test')"));
        $this->assertEquals(1, $driver->execute("INSERT INTO test (name) VALUES ('test')"));
        $this->assertEquals(2, $driver->execute("UPDATE test SET name='retest' WHERE name='test'"));
    }

    public function testExecuteRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $driver->execute("SELECT count(*) FROM test");
    }

    public function testPrepareSetLastQuery()
    {
        $sql = "SELECT id, name FROM test WHERE id = :id";
        $driver = new PdoDriver($this->getPdo());
        $driver->prepare($sql);
        $this->assertEquals($sql, $driver->getLastQuery());
    }

    public function testQuerySetLastQuery()
    {
        $sql = "SELECT count(*) FROM test";
        $driver = new PdoDriver($this->getPdo());
        $driver->query($sql);
        $this->assertEquals($sql, $driver->getLastQuery());
    }

    public function testExecuteSetLastQuery()
    {
        $sql = "SELECT count(*) FROM test";
        $driver = new PdoDriver($this->getPdo());
        $driver->execute($sql);
        $this->assertEquals($sql, $driver->getLastQuery());
    }

    public function testGetLastError()
    {
        $driver = new PdoDriver($this->getPdo());
        $driver->execute("SELECT * FROM undefined_table_called_by_testGetLastError");
        $this->assertStringContainsString('undefined_table_called_by_testGetLastError', $driver->getLastError());
    }

    public function testGetLastErrorRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->execute("SELECT * FROM undefined_table_called_by_testGetLastError");
        $driver->disconnect();
        $driver->getLastError();
    }

    public function testLastInsertId()
    {
        $driver = new PdoDriver($this->getPdo());
        $driver->execute("INSERT INTO test (name) VALUES ('test')");
        $this->assertEquals(1, $driver->lastInsertId());
        $driver->execute("INSERT INTO test (name) VALUES ('test')");
        $this->assertEquals(2, $driver->lastInsertId());
    }

    public function testLastInsertIdRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->execute("INSERT INTO test (name) VALUES ('test')");
        $driver->disconnect();
        $driver->lastInsertId();
    }

    public function testGetMetaTables()
    {
        $driver = new PdoDriver($this->getPdo());
        $tables = $driver->getMetaTables();
        $this->assertArrayHasKey('test', $tables);
        $this->assertInstanceOf(Table::class, $tables['test']);
    }

    public function testGetMetaColumns()
    {
        $driver = new PdoDriver($this->getPdo());
        $columns = $driver->getMetaColumns('test');
        $this->assertArrayHasKey('id', $columns);
        $this->assertArrayHasKey('name', $columns);
        $this->assertInstanceOf(Column::class, $columns['id']);
        $this->assertInstanceOf(Column::class, $columns['name']);
    }

    public function testGetMetaTablesRaiseExceptionWhenMetaDataProcessorIsNotSet()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver();
        $driver->getMetaTables();
    }

    public function testGetMetaColumnsRaiseExceptionWhenMetaDataProcessorIsNotSet()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver();
        $driver->getMetaColumns('test');
    }

    public function testQuote()
    {
        $driver = new PdoDriver($this->getPdo());
        $this->assertEquals("'Foo'", $driver->quote('Foo'));
        $this->assertEquals("'''Foo'''", $driver->quote("'Foo'"));
    }

    public function testQuoteRaiseExceptionWhenAfterDisconnected()
    {
        $this->expectException(\RuntimeException::class);
        $driver = new PdoDriver($this->getPdo());
        $driver->disconnect();
        $driver->quote('Foo');
    }

    public function testEscapeCharacter()
    {
        $driver = new PdoDriver($this->getPdo());
        $driver->setEscapeCharacter('!');
        $this->assertEquals('!%Foo!%', $driver->escapeLikePattern('%Foo%'));
        $this->assertEquals('!_Foo!_', $driver->escapeLikePattern('_Foo_'));
    }

    public function testPrepareEscapeLikePattern()
    {
        $driver = new PdoDriver($this->getPdo());

        $statement = $driver->prepare(
            "INSERT INTO test (name) VALUES (:name)"
        );
        $statement->execute(['name' => 'Foo-%RIK%-1']);
        $statement->execute(['name' => 'Bar-%RIK%-2']);
        $statement->execute(['name' => 'Baz-%RIK%-3']);
        $statement = $driver->prepare(
            "SELECT id, name FROM test WHERE name LIKE :name ESCAPE '\\'"
        );
        $statement->setFetchMode(Statement::FETCH_ASSOC);

        $statement->execute([
            'name' => '%' . $driver->escapeLikePattern('%RIK%') . '%',
        ]);

        $user = $statement->fetch();
        $this->assertEquals('Foo-%RIK%-1', $user['name']);

        $user = $statement->fetch();
        $this->assertEquals('Bar-%RIK%-2', $user['name']);

        $user = $statement->fetch();
        $this->assertEquals('Baz-%RIK%-3', $user['name']);

        $statement->execute([
            'name' => $driver->escapeLikePattern('Foo-%') . '%',
        ]);
        $user = $statement->fetch();
        $this->assertEquals('Foo-%RIK%-1', $user['name']);

        $statement->execute([
            'name' => $driver->escapeLikePattern('Bar-%') . '%',
        ]);
        $user = $statement->fetch();
        $this->assertEquals('Bar-%RIK%-2', $user['name']);

        $statement->execute([
            'name' => $driver->escapeLikePattern('Baz-%') . '%',
        ]);
        $user = $statement->fetch();
        $this->assertEquals('Baz-%RIK%-3', $user['name']);
    }

}
