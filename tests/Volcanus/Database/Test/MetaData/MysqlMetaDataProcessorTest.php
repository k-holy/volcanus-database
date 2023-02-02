<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\MetaData;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Driver\StatementInterface;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\MetaData\MysqlMetaDataProcessor;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;

/**
 * Test for MysqlMetaDataProcessor
 *
 * @author k.holy74@gmail.com
 */
class MysqlMetaDataProcessorTest extends \PHPUnit\Framework\TestCase
{

    public function testGetMetaTables()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaTables = $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables());

        $this->assertArrayHasKey('users', $metaTables);
        $this->assertInstanceOf(Table::class, $metaTables['users']);

        $this->assertArrayHasKey('messages', $metaTables);
        $this->assertInstanceOf(Table::class, $metaTables['messages']);
    }

    public function testGetMetaTablesFromCache()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaTablesCache = $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables());

        /** @var $cacheProcessorInterface CacheProcessorInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProcessorInterface = $this->createMock(CacheProcessorInterface::class);
        $cacheProcessorInterface->expects($this->once())
            ->method('hasMetaTables')
            ->will($this->returnValue(true));
        $cacheProcessorInterface->expects($this->once())
            ->method('getMetaTables')
            ->will($this->returnValue($metaTablesCache));

        $metaDataProcessor = new MysqlMetaDataProcessor($cacheProcessorInterface);

        $this->assertEquals($metaTablesCache, $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables()));
    }


    public function testGetMetaTablesSaveToCache()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaTablesCache = $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables());

        /** @var $cacheProcessorInterface CacheProcessorInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProcessorInterface = $this->createMock(CacheProcessorInterface::class);
        $cacheProcessorInterface->expects($this->once())
            ->method('hasMetaTables')
            ->will($this->returnValue(false));
        $cacheProcessorInterface->expects($this->once())
            ->method('setMetaTables')
            ->with($this->equalTo($metaTablesCache));

        $metaDataProcessor = new MysqlMetaDataProcessor($cacheProcessorInterface);
        $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables());
    }

    public function testGetMetaTablesName()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaTables = $metaDataProcessor->getMetaTables($this->getDriverForGetMetaTables());

        $this->assertEquals('users', $metaTables['users']->name);
        $this->assertEquals('messages', $metaTables['messages']->name);
    }

    public function testGetMetaColumns()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertArrayHasKey('user_id', $metaColumns);
        $column = $metaColumns['user_id'];
        $this->assertInstanceOf(Column::class, $column);
        $this->assertFalse($column->binary);

        $this->assertArrayHasKey('user_type', $metaColumns);
        $column = $metaColumns['user_type'];
        $this->assertInstanceOf(Column::class, $column);
        $this->assertFalse($column->binary);

        $this->assertArrayHasKey('user_name', $metaColumns);
        $column = $metaColumns['user_name'];
        $this->assertInstanceOf(Column::class, $column);
        $this->assertFalse($column->binary);
    }

    public function testGetMetaColumnsFromCache()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumnsCache = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        /** @var $cacheProcessorInterface CacheProcessorInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProcessorInterface = $this->createMock(CacheProcessorInterface::class);
        $cacheProcessorInterface->expects($this->once())
            ->method('hasMetaColumns')
            ->will($this->returnValue(true));
        $cacheProcessorInterface->expects($this->once())
            ->method('getMetaColumns')
            ->will($this->returnValue($metaColumnsCache));

        $metaDataProcessor = new MysqlMetaDataProcessor($cacheProcessorInterface);

        $this->assertEquals($metaColumnsCache, $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users'));
    }

    public function testGetMetaColumnsSaveToCache()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumnsCache = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        /** @var $cacheProcessorInterface CacheProcessorInterface|\PHPUnit\Framework\MockObject\MockObject */
        $cacheProcessorInterface = $this->createMock(CacheProcessorInterface::class);
        $cacheProcessorInterface->expects($this->once())
            ->method('hasMetaColumns')
            ->will($this->returnValue(false));
        $cacheProcessorInterface->expects($this->once())
            ->method('setMetaColumns')
            ->with(
                $this->equalTo('users'),
                $this->equalTo($metaColumnsCache)
            );

        $metaDataProcessor = new MysqlMetaDataProcessor($cacheProcessorInterface);
        $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');
    }

    public function testGetColumnName()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertEquals('user_id', $metaColumns['user_id']->name);
        $this->assertEquals('user_type', $metaColumns['user_type']->name);
        $this->assertEquals('user_gender', $metaColumns['user_gender']->name);
        $this->assertEquals('user_name', $metaColumns['user_name']->name);
        $this->assertEquals('user_decimal', $metaColumns['user_decimal']->name);
        $this->assertEquals('user_binary', $metaColumns['user_binary']->name);
    }

    public function testGetColumnType()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertEquals('int', $metaColumns['user_id']->type);
        $this->assertEquals('int', $metaColumns['user_type']->type);
        $this->assertEquals('enum', $metaColumns['user_gender']->type);
        $this->assertEquals('varchar', $metaColumns['user_key']->type);
        $this->assertEquals('varchar', $metaColumns['user_name']->type);
        $this->assertEquals('decimal', $metaColumns['user_decimal']->type);
        $this->assertEquals('blob', $metaColumns['user_binary']->type);
    }

    public function testGetColumnMaxLength()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertEquals('10', $metaColumns['user_id']->maxLength);
        $this->assertEquals('11', $metaColumns['user_type']->maxLength);
        $this->assertEquals('6', $metaColumns['user_gender']->maxLength);
        $this->assertEquals('64', $metaColumns['user_key']->maxLength);
        $this->assertEquals('255', $metaColumns['user_name']->maxLength);
        $this->assertEquals('10', $metaColumns['user_decimal']->maxLength);
        $this->assertNull($metaColumns['user_binary']->maxLength);
    }

    public function testGetColumnScale()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertNull($metaColumns['user_id']->scale);
        $this->assertNull($metaColumns['user_type']->scale);
        $this->assertNull($metaColumns['user_gender']->scale);
        $this->assertNull($metaColumns['user_key']->scale);
        $this->assertNull($metaColumns['user_name']->scale);
        $this->assertEquals('5', $metaColumns['user_decimal']->scale);
        $this->assertNull($metaColumns['user_binary']->scale);
    }

    public function testGetColumnIsBinary()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertFalse($metaColumns['user_id']->binary);
        $this->assertFalse($metaColumns['user_type']->binary);
        $this->assertFalse($metaColumns['user_gender']->binary);
        $this->assertFalse($metaColumns['user_key']->binary);
        $this->assertFalse($metaColumns['user_name']->binary);
        $this->assertFalse($metaColumns['user_decimal']->binary);
        $this->assertTrue($metaColumns['user_binary']->binary);
    }

    public function testGetColumnDefault()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertNull($metaColumns['user_id']->default);
        $this->assertEquals('1', $metaColumns['user_type']->default);
        $this->assertNull($metaColumns['user_gender']->default);
        $this->assertNull($metaColumns['user_key']->default);
        $this->assertNull($metaColumns['user_name']->default);
        $this->assertNull($metaColumns['user_decimal']->default);
        $this->assertNull($metaColumns['user_binary']->default);
    }

    public function testGetColumnIsNotNull()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertTrue($metaColumns['user_id']->notNull);
        $this->assertTrue($metaColumns['user_type']->notNull);
        $this->assertTrue($metaColumns['user_gender']->notNull);
        $this->assertTrue($metaColumns['user_key']->notNull);
        $this->assertFalse($metaColumns['user_name']->notNull);
        $this->assertFalse($metaColumns['user_decimal']->notNull);
        $this->assertFalse($metaColumns['user_binary']->notNull);
    }

    public function testGetColumnIsPrimaryKey()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertTrue($metaColumns['user_id']->primaryKey);
        $this->assertFalse($metaColumns['user_type']->primaryKey);
        $this->assertFalse($metaColumns['user_gender']->primaryKey);
        $this->assertFalse($metaColumns['user_key']->primaryKey);
        $this->assertFalse($metaColumns['user_name']->primaryKey);
        $this->assertFalse($metaColumns['user_decimal']->primaryKey);
        $this->assertFalse($metaColumns['user_binary']->primaryKey);
    }

    public function testGetColumnIsUniqueKey()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertFalse($metaColumns['user_id']->uniqueKey);
        $this->assertFalse($metaColumns['user_type']->uniqueKey);
        $this->assertFalse($metaColumns['user_gender']->uniqueKey);
        $this->assertTrue($metaColumns['user_key']->uniqueKey);
        $this->assertFalse($metaColumns['user_name']->uniqueKey);
        $this->assertFalse($metaColumns['user_decimal']->uniqueKey);
        $this->assertFalse($metaColumns['user_binary']->uniqueKey);
    }

    public function testGetColumnIsAutoIncrement()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertTrue($metaColumns['user_id']->autoIncrement);
        $this->assertFalse($metaColumns['user_type']->autoIncrement);
        $this->assertFalse($metaColumns['user_gender']->autoIncrement);
        $this->assertFalse($metaColumns['user_key']->autoIncrement);
        $this->assertFalse($metaColumns['user_name']->autoIncrement);
        $this->assertFalse($metaColumns['user_decimal']->autoIncrement);
        $this->assertFalse($metaColumns['user_binary']->autoIncrement);
    }

    public function testGetColumnComment()
    {
        $metaDataProcessor = new MysqlMetaDataProcessor();
        $metaColumns = $metaDataProcessor->getMetaColumns($this->getDriverForGetMetaColumnsOfUsers(), 'users');

        $this->assertEquals('ユーザーID', $metaColumns['user_id']->comment);
        $this->assertEquals('ユーザー種別', $metaColumns['user_type']->comment);
        $this->assertEquals('ユーザー性別', $metaColumns['user_gender']->comment);
        $this->assertEquals('ユーザー識別子', $metaColumns['user_key']->comment);
        $this->assertEquals('ユーザー名', $metaColumns['user_name']->comment);
        $this->assertEquals('ユーザー小数', $metaColumns['user_decimal']->comment);
        $this->assertEquals('ユーザーバイナリ', $metaColumns['user_binary']->comment);
    }

    private function getDriverForGetMetaTables()
    {
        /** @var $statementInterface StatementInterface|\PHPUnit\Framework\MockObject\MockObject */
        $statementInterface = $this->createMock(StatementInterface::class);
        $statementInterface->expects($this->any())
            ->method('setFetchMode')
            ->will($this->returnValue(true));
        $statementInterface->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(
                new \ArrayIterator(
                    [
                        [0 => 'users'],
                        [0 => 'messages'],
                    ]
                )
            ));

        /** @var $driverInterface DriverInterface|\PHPUnit\Framework\MockObject\MockObject */
        $driverInterface = $this->createMock(DriverInterface::class);
        $driverInterface->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statementInterface));

        return $driverInterface;
    }

    private function getDriverForGetMetaColumnsOfUsers()
    {
        /** @var $statementInterface StatementInterface|\PHPUnit\Framework\MockObject\MockObject */
        $statementInterface = $this->createMock(StatementInterface::class);
        $statementInterface->expects($this->any())
            ->method('setFetchMode')
            ->will($this->returnValue(true));
        $statementInterface->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(
                new \ArrayIterator(
                /*
                CREATE TABLE users(
                     user_id      INTEGER UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY  comment 'ユーザーID'
                    ,user_type    INTEGER                         NOT NULL DEFAULT 1    comment 'ユーザー種別'
                    ,user_gender  ENUM('female', 'male')          NOT NULL              comment 'ユーザー性別'
                    ,user_key     VARCHAR(64)  BINARY             NOT NULL UNIQUE       comment 'ユーザー識別子'
                    ,user_name    VARCHAR(255)                             DEFAULT NULL comment 'ユーザー名'
                    ,user_decimal DECIMAL(10,5)                                         comment 'ユーザー小数'
                    ,user_binary  BLOB                                                  comment 'ユーザーバイナリ'
                ) comment 'ユーザー情報';
                */
                    [
                        [
                            'Field' => 'user_id',
                            'Type' => 'int(10) unsigned',
                            'Collation' => 'NULL',
                            'Null' => 'NO',
                            'Key' => 'PRI',
                            'Default' => 'NULL',
                            'Extra' => 'auto_increment',
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザーID',
                        ],
                        [
                            'Field' => 'user_type',
                            'Type' => 'int(11)',
                            'Collation' => 'NULL',
                            'Null' => 'NO',
                            'Key' => null,
                            'Default' => '1',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザー種別',
                        ],
                        [
                            'Field' => 'user_gender',
                            'Type' => 'enum(\'female\', \'male\')',
                            'Collation' => 'NULL',
                            'Null' => 'NO',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザー性別',
                        ],
                        [
                            'Field' => 'user_key',
                            'Type' => 'varchar(64)',
                            'Collation' => 'utf8_bin',
                            'Null' => 'NO',
                            'Key' => 'UNI',
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザー識別子',
                        ],
                        [
                            'Field' => 'user_name',
                            'Type' => 'varchar(255)',
                            'Collation' => 'utf8_general_ci',
                            'Null' => 'YES',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザー名',
                        ],
                        [
                            'Field' => 'user_decimal',
                            'Type' => 'decimal(10,5)',
                            'Collation' => null,
                            'Null' => 'YES',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザー小数',
                        ],
                        [
                            'Field' => 'user_binary',
                            'Type' => 'blob',
                            'Collation' => null,
                            'Null' => 'YES',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'ユーザーバイナリ',
                        ],
                    ]
                )
            ));

        /** @var $driverInterface DriverInterface|\PHPUnit\Framework\MockObject\MockObject */
        $driverInterface = $this->createMock(DriverInterface::class);
        $driverInterface->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statementInterface));

        return $driverInterface;
    }

    private function getDriverForGetMetaColumnsOfMessages()
    {
        /** @var $statementInterface StatementInterface|\PHPUnit\Framework\MockObject\MockObject */
        $statementInterface = $this->createMock(StatementInterface::class);
        $statementInterface->expects($this->any())
            ->method('setFetchMode')
            ->will($this->returnValue(true));
        $statementInterface->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(
                new \ArrayIterator(
                /*
                CREATE TABLE messages(
                     message_id  INTEGER UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY comment 'メッセージID'
                    ,title       VARCHAR(255)                    NOT NULL             comment 'タイトル'
                    ,description TEXT             BINARY                              comment 'メッセージ'
                    ,posted_by   INTEGER UNSIGNED                NOT NULL             comment '投稿者'
                    ,FOREIGN KEY(posted_by) REFERENCES users(user_id) ON DELETE CASCADE
                ) comment 'メッセージ情報';
                */
                    [
                        [
                            'Field' => 'message_id',
                            'Type' => 'int(10) unsigned',
                            'Collation' => 'NULL',
                            'Null' => 'NO',
                            'Key' => 'PRI',
                            'Default' => 'NULL',
                            'Extra' => 'auto_increment',
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'メッセージID',
                        ],
                        [
                            'Field' => 'title',
                            'Type' => 'varchar(255)',
                            'Collation' => 'utf8_general_ci',
                            'Null' => 'NO',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'タイトル',
                        ],
                        [
                            'Field' => 'description',
                            'Type' => 'text',
                            'Collation' => 'utf8_bin',
                            'Null' => 'YES',
                            'Key' => null,
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => 'メッセージ',
                        ],
                        [
                            'Field' => 'posted_by',
                            'Type' => 'int(10) unsigned',
                            'Collation' => null,
                            'Null' => 'NO',
                            'Key' => 'MUL',
                            'Default' => 'NULL',
                            'Extra' => null,
                            'Privileged' => 'select,insert,update,references',
                            'Comment' => '投稿者',
                        ],
                    ]
                )
            ));

        /** @var $driverInterface DriverInterface|\PHPUnit\Framework\MockObject\MockObject */
        $driverInterface = $this->createMock(DriverInterface::class);
        $driverInterface->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statementInterface));

        return $driverInterface;
    }

}
