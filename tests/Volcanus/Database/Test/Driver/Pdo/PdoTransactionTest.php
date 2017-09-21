<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

use Volcanus\Database\Driver\Pdo\PdoTransaction;
use Volcanus\Database\Driver\Pdo\PdoDriver;
use Volcanus\Database\Statement;

/**
 * Test for PdoTransaction
 *
 * @author k.holy74@gmail.com
 */
class PdoTransactionTest extends \PHPUnit_Framework_TestCase
{

    /** @var \PDO */
    private static $pdo;

    public function tearDown()
    {
        $this->getPdo()->exec("DELETE FROM test");
        $this->getPdo()->exec("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'test'");
    }

    public function getPdo()
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO('sqlite::memory:');
            static::$pdo->exec(<<<SQL
CREATE TABLE test(
     id         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL
    ,name       TEXT
);
SQL
            );
        }
        return static::$pdo;
    }

    public function testRollback()
    {
        $pdo = $this->getPdo();
        $transaction = new PdoTransaction($pdo);
        $transaction->begin();
        $pdo->exec("INSERT INTO test (name) VALUES ('test')");
        $count = $pdo->query("SELECT count(*) FROM test")->fetch(\PDO::FETCH_NUM);
        $this->assertEquals('1', $count[0]);

        $transaction->rollback();
        $count = $pdo->query("SELECT count(*) FROM test")->fetch(\PDO::FETCH_NUM);
        $this->assertEquals('0', $count[0]);
    }

    public function testCommit()
    {
        $pdo = $this->getPdo();
        $transaction = new PdoTransaction($pdo);
        $transaction->begin();
        $pdo->exec("INSERT INTO test (name) VALUES ('test')");
        $transaction->commit();
        $count = $pdo->query("SELECT count(*) FROM test")->fetch(\PDO::FETCH_NUM);
        $this->assertEquals('1', $count[0]);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testBeginRaiseRuntimeException()
    {
        /** @var $pdo \Volcanus\Database\Test\Driver\Pdo\PdoMock|\PHPUnit_Framework_MockObject_MockObject */
        $pdo = $this->getMock('\Volcanus\Database\Test\Driver\Pdo\PdoMock');
        $pdo->expects($this->once())
            ->method('beginTransaction')
            ->will($this->throwException(new \PDOException()));

        $transaction = new PdoTransaction($pdo);
        $transaction->begin();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCommitRaiseRuntimeException()
    {
        /** @var $pdo \Volcanus\Database\Test\Driver\Pdo\PdoMock|\PHPUnit_Framework_MockObject_MockObject */
        $pdo = $this->getMock('\Volcanus\Database\Test\Driver\Pdo\PdoMock');
        $pdo->expects($this->once())
            ->method('commit')
            ->will($this->throwException(new \PDOException()));

        $transaction = new PdoTransaction($pdo);
        $transaction->commit();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testRollbackRaiseRuntimeException()
    {
        /** @var $pdo \Volcanus\Database\Test\Driver\Pdo\PdoMock|\PHPUnit_Framework_MockObject_MockObject */
        $pdo = $this->getMock('\Volcanus\Database\Test\Driver\Pdo\PdoMock');
        $pdo->expects($this->once())
            ->method('rollback')
            ->will($this->throwException(new \PDOException()));

        $transaction = new PdoTransaction($pdo);
        $transaction->rollback();
    }

}
