<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

use Volcanus\Database\Statement;
use Volcanus\Database\Driver\Pdo\PdoStatement;

/**
 * Test for PdoStatement
 *
 * @author k.holy74@gmail.com
 */
class PdoStatementTest extends \PHPUnit\Framework\TestCase
{

    /** @var \PDO */
    private static \PDO $pdo;

    public function tearDown(): void
    {
        $this->getPdo()->exec("DELETE FROM users");
        $this->getPdo()->exec("UPDATE SQLITE_SEQUENCE SET seq = 0 WHERE name = 'users'");
    }

    private function getPdo(): \PDO
    {
        if (!isset(static::$pdo)) {
            static::$pdo = new \PDO('sqlite::memory:', null, null, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]);
            static::$pdo->exec('DROP TABLE IF EXISTS users;');
            static::$pdo->exec(<<<'SQL'
CREATE TABLE users(
  user_id    INTEGER     NOT NULL PRIMARY KEY AUTOINCREMENT 
, user_name  TEXT
, birthday   VARCHAR(10)
, created_at INTEGER     NOT NULL
);
SQL
            );
        }
        return static::$pdo;
    }

    private function insertUser($parameters)
    {
        $pdo = $this->getPdo();
        $pdo->beginTransaction();
        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
INSERT INTO users (
  user_name
, birthday
, created_at
) VALUES (
  :user_name
, :birthday
, :created_at
)
SQL
        ));
        $statement->execute($parameters);
        $pdo->commit();
    }

    public function testExecuteParamInt()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => null,
            'birthday' => null,
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE created_at = :created_at"));
        $statement->execute(['created_at' => $now->getTimestamp()]);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $user = $statement->fetch();

        $this->assertEquals('1', $user['user_id']);
        $this->assertEquals($now->getTimestamp(), $user['created_at']);
    }

    public function testExecuteParamStr()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => null,
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE user_name = :user_name"));
        $statement->execute(['user_name' => 'test1']);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $user = $statement->fetch();

        $this->assertEquals('1', $user['user_id']);
        $this->assertEquals('test1', $user['user_name']);
    }

    public function testExecuteParamNull()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => null,
            'birthday' => null,
            'created_at' => $now->getTimestamp(),
        ]);
        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE user_id = 1"));
        $statement->execute();
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $user = $statement->fetch();

        $this->assertNull($user['user_name']);
        $this->assertNull($user['birthday']);
    }

    public function testExecuteRaiseExceptionWhenPDOExceptionIsThrown()
    {
        $this->expectException(\RuntimeException::class);
        $statement = new PDOStatement(
            $this->getPdo()->prepare("SELECT * FROM users WHERE user_id = :user_id")
        );
        $statement->execute([
            'user_name' => 'test1',
        ]);
    }

    public function testSetFetchModeRaiseInvalidArgumentExceptionWhenUnsupportedFetchMode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $statement = new PdoStatement(
            $this->getPdo()->query("SELECT count(*) AS cnt FROM users")
        );
        $statement->setFetchMode(9999999);
    }

    public function testFetchAssoc()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id
, user_name
, birthday
, created_at
FROM
  users
WHERE
  user_id = :user_id
SQL
        ));
        $statement->execute(['user_id' => 1]);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $user = $statement->fetch();

        $this->assertEquals('1', $user['user_id']);
        $this->assertEquals('test1', $user['user_name']);
        $this->assertEquals('1980-12-20', $user['birthday']);
        $this->assertEquals($now->getTimestamp(), $user['created_at']);
    }

    public function testFetchNum()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id
, user_name
, birthday
, created_at
FROM
  users
WHERE
  user_id = :user_id
SQL
        ));
        $statement->execute(['user_id' => 1]);
        $statement->setFetchMode(Statement::FETCH_NUM);
        $user = $statement->fetch();

        $this->assertEquals('1', $user[0]);
        $this->assertEquals('test1', $user[1]);
        $this->assertEquals('1980-12-20', $user[2]);
        $this->assertEquals($now->getTimestamp(), $user[3]);
    }

    public function testFetchClass()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id AS userId
, user_name AS userName
, birthday AS birthday
, created_at AS createdAt
FROM
  users
WHERE
  user_id = :userId
SQL
        ));
        $statement->execute(['userId' => 1]);
        $statement->setFetchMode(Statement::FETCH_CLASS,
            PdoStatementTestData::class
        );
        $user = $statement->fetch();

        $this->assertInstanceOf(PdoStatementTestData::class, $user);
        $this->assertEquals('1', $user->userId);
        $this->assertEquals('test1', $user->userName);
        $this->assertEquals('1980-12-20', $user->birthday);
        $this->assertEquals($now->getTimestamp(), $user->createdAt);
        $this->assertNull($user->age);
    }

    public function testFetchClassWithArguments()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id AS userId
, user_name AS userName
, birthday AS birthday
, created_at AS createdAt
FROM
  users
WHERE
  user_id = :userId
SQL
        ));
        $statement->execute(['userId' => 1]);
        $statement->setFetchMode(Statement::FETCH_CLASS,
            PdoStatementTestData::class,
            [
                [
                    'now' => $now,
                ],
            ]
        );
        $user = $statement->fetch();

        $this->assertInstanceOf(PdoStatementTestData::class, $user);
        $this->assertEquals('1', $user->userId);
        $this->assertEquals('test1', $user->userName);
        $this->assertEquals('1980-12-20', $user->birthday);
        $this->assertEquals($now->getTimestamp(), $user->createdAt);
        $this->assertEquals(33, $user->age);
    }

    public function testFetchReturnFalseWhenCannotContinue()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id
, user_name
, birthday
, created_at
FROM
  users
WHERE
  user_id = :user_id
SQL
        ));
        $statement->execute(['user_id' => 1]);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $user = $statement->fetch();

        $this->assertFalse($statement->fetch());
    }

    public function testFetchCallback()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE user_id = :userId"));
        $statement->execute(['userId' => 1]);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $statement->setFetchCallback(function ($cols) use ($now) {
            return new PdoStatementTestData([
                'userId' => (int)$cols['user_id'],
                'userName' => $cols['user_name'],
                'birthday' => $cols['birthday'],
                'createdAt' => $cols['created_at'],
                'now' => $now,
            ]);
        });
        $user = $statement->fetch();

        $this->assertInstanceOf(PdoStatementTestData::class, $user);
        $this->assertEquals(1, $user->userId);
        $this->assertEquals('test1', $user->userName);
        $this->assertEquals('1980-12-20', $user->birthday);
        $this->assertEquals($now->getTimestamp(), $user->createdAt);
        $this->assertEquals(33, $user->age);
    }

    /** @noinspection PhpUnusedParameterInspection */
    public function testFetchCallbackReturnedFalseWhenFetchReturnedFalse()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE user_id = :userId"));
        $statement->execute(['userId' => 1000]);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $statement->setFetchCallback(function ($cols) use ($now) {
            return true;
        });
        $this->assertFalse($statement->fetch());
    }

    public function testFetchCallbackInIteration()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $this->insertUser([
            'user_name' => 'test2',
            'birthday' => '1996-01-01',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare("SELECT * FROM users WHERE user_id = :userId"));
        $statement->execute(['userId' => 1]);
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $statement->setFetchCallback(function ($cols) use ($now) {
            return new PdoStatementTestData([
                'userId' => (int)$cols['user_id'],
                'userName' => $cols['user_name'],
                'birthday' => $cols['birthday'],
                'createdAt' => $cols['created_at'],
                'now' => $now,
            ]);
        });

        foreach ($statement as $user) {
            $this->assertInstanceOf(PdoStatementTestData::class, $user);
            $this->assertEquals($now->getTimestamp(), $user->createdAt);
            switch ($user->userId) {
                case 1:
                    $this->assertEquals('test1', $user->userName);
                    $this->assertEquals('1980-12-20', $user->birthday);
                    $this->assertEquals(33, $user->age);
                    break;
                case 2:
                    $this->assertEquals('test2', $user->userName);
                    $this->assertEquals('1996-01-01', $user->birthday);
                    $this->assertEquals(17, $user->age);
                    break;
            }
        }
    }

    public function testFetchAllByFetchAssoc()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $this->insertUser([
            'user_name' => 'test2',
            'birthday' => '1996-01-01',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id
, user_name
, birthday
, created_at
FROM
  users
ORDER BY
  user_id
SQL
        ));
        $statement->execute();
        $statement->setFetchMode(Statement::FETCH_ASSOC);
        $users = $statement->fetchAll();

        $this->assertCount(2, $users);

        $this->assertEquals('1', $users[0]['user_id']);
        $this->assertEquals('test1', $users[0]['user_name']);
        $this->assertEquals('1980-12-20', $users[0]['birthday']);
        $this->assertEquals($now->getTimestamp(), $users[0]['created_at']);

        $this->assertEquals('2', $users[1]['user_id']);
        $this->assertEquals('test2', $users[1]['user_name']);
        $this->assertEquals('1996-01-01', $users[1]['birthday']);
        $this->assertEquals($now->getTimestamp(), $users[1]['created_at']);
    }

    public function testFetchAllByFetchNum()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $this->insertUser([
            'user_name' => 'test2',
            'birthday' => '1996-01-01',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id
, user_name
, birthday
, created_at
FROM
  users
ORDER BY
  user_id
SQL
        ));
        $statement->execute();
        $statement->setFetchMode(Statement::FETCH_NUM);
        $users = $statement->fetchAll();

        $this->assertCount(2, $users);

        $this->assertEquals('1', $users[0][0]);
        $this->assertEquals('test1', $users[0][1]);
        $this->assertEquals('1980-12-20', $users[0][2]);
        $this->assertEquals($now->getTimestamp(), $users[0][3]);

        $this->assertEquals('2', $users[1][0]);
        $this->assertEquals('test2', $users[1][1]);
        $this->assertEquals('1996-01-01', $users[1][2]);
        $this->assertEquals($now->getTimestamp(), $users[1][3]);
    }

    public function testFetchAllByFetchClass()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $this->insertUser([
            'user_name' => 'test2',
            'birthday' => '1996-01-01',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id AS userId
, user_name AS userName
, birthday AS birthday
, created_at AS createdAt
FROM
  users
ORDER BY
  user_id
SQL
        ));
        $statement->execute();
        $statement->setFetchMode(Statement::FETCH_CLASS,
            PdoStatementTestData::class
        );
        $users = $statement->fetchAll();

        $this->assertCount(2, $users);

        $this->assertInstanceOf(PdoStatementTestData::class, $users[0]);
        $this->assertEquals('1', $users[0]->userId);
        $this->assertEquals('test1', $users[0]->userName);
        $this->assertEquals('1980-12-20', $users[0]->birthday);
        $this->assertEquals($now->getTimestamp(), $users[0]->createdAt);
        $this->assertNull($users[0]->age);

        $this->assertInstanceOf(PdoStatementTestData::class, $users[1]);
        $this->assertEquals('2', $users[1]->userId);
        $this->assertEquals('test2', $users[1]->userName);
        $this->assertEquals('1996-01-01', $users[1]->birthday);
        $this->assertEquals($now->getTimestamp(), $users[1]->createdAt);
        $this->assertNull($users[1]->age);

    }

    public function testFetchAllByFetchClassWithArguments()
    {
        $pdo = $this->getPdo();

        $now = \DateTime::createFromFormat('Y-m-d H:i:s', '2013-12-20 00:00:00');

        $this->insertUser([
            'user_name' => 'test1',
            'birthday' => '1980-12-20',
            'created_at' => $now->getTimestamp(),
        ]);

        $this->insertUser([
            'user_name' => 'test2',
            'birthday' => '1996-01-01',
            'created_at' => $now->getTimestamp(),
        ]);

        $statement = new PdoStatement($pdo->prepare(<<<'SQL'
SELECT
  user_id AS userId
, user_name AS userName
, birthday AS birthday
, created_at AS createdAt
FROM
  users
ORDER BY
  user_id
SQL
        ));
        $statement->execute();
        $statement->setFetchMode(Statement::FETCH_CLASS,
            PdoStatementTestData::class,
            [
                [
                    'now' => $now,
                ],
            ]);
        $users = $statement->fetchAll();

        $this->assertCount(2, $users);

        $this->assertInstanceOf(PdoStatementTestData::class, $users[0]);
        $this->assertEquals('1', $users[0]->userId);
        $this->assertEquals('test1', $users[0]->userName);
        $this->assertEquals('1980-12-20', $users[0]->birthday);
        $this->assertEquals($now->getTimestamp(), $users[0]->createdAt);
        $this->assertEquals(33, $users[0]->age);

        $this->assertInstanceOf(PdoStatementTestData::class, $users[1]);
        $this->assertEquals('2', $users[1]->userId);
        $this->assertEquals('test2', $users[1]->userName);
        $this->assertEquals('1996-01-01', $users[1]->birthday);
        $this->assertEquals($now->getTimestamp(), $users[1]->createdAt);
        $this->assertEquals(17, $users[1]->age);

    }

}
