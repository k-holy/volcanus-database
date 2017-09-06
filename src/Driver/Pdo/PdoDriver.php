<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Driver\AbstractDriver;
use Volcanus\Database\Driver\Pdo\PdoFactory;
use Volcanus\Database\Dsn;
use Volcanus\Database\MetaData\MetaDataProcessorInterface;

/**
 * PDOコネクション
 *
 * @property \Volcanus\Database\Dsn $dsn
 * @property \Volcanus\Database\MetaData\MetaDataProcessorInterface $metaDataProcessor
 * @property string $lastQuery
 * @property string $escapeCharacter
 *
 * @author k_horii@rikcorp.jp
 */
class PdoDriver extends AbstractDriver
{

    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * コンストラクタ
     *
     * @param \PDO $pdo
     * @param \Volcanus\Database\MetaData\MetaDataProcessorInterface $metaDataProcessor
     */
    public function __construct(\PDO $pdo = null, MetaDataProcessorInterface $metaDataProcessor = null)
    {
        $this->pdo = null;
        $this->lastQuery = null;
        if (isset($pdo)) {
            $this->pdo = $pdo;
            if (!isset($metaDataProcessor)) {
                $metaDataProcessor = $this->createMetaDataProcessor();
            }
        }
        if (isset($metaDataProcessor)) {
            $this->setMetaDataProcessor($metaDataProcessor);
        }
    }

    /**
     * DSNからインスタンスを生成します。
     *
     * @param \Volcanus\Database\Dsn
     * @return static
     */
    public static function createFromDsn(Dsn $dsn)
    {
        $driver = new static(PdoFactory::createFromDsn($dsn));
        $driver->setDsn($dsn);
        return $driver;
    }

    /**
     * DBに接続します。
     *
     * @param \Volcanus\Database\Dsn $dsn DSNオブジェクト
     * @return $this
     */
    public function connect(Dsn $dsn = null)
    {
        if (isset($dsn)) {
            $this->dsn = $dsn;
        }
        $this->pdo = PdoFactory::createFromDsn($this->dsn);
        return $this;
    }

    /**
     * DBとの接続を解放します。
     *
     * @return bool
     */
    public function disconnect()
    {
        $this->pdo = null;
        return true;
    }

    /**
     * DBと接続中かどうかを返します。
     *
     * @return bool
     */
    public function connected()
    {
        return isset($this->pdo);
    }

    /**
     * ドライバ名を返します。
     *
     * @return string|null ドライバ名
     */
    public function getDriverName()
    {
        if (isset($this->pdo)) {
            return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        }
        return null;
    }

    /**
     * 最後に発生したエラーを返します。
     *
     * @return string|null
     */
    public function getLastError()
    {
        $errors = $this->pdo->errorInfo();
        return (isset($errors[2])) ? $errors[2] : null;
    }

    /**
     * 直近のinsert操作で生成されたIDを返します。
     *
     * @return mixed 実行結果
     */
    public function lastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * 文字列を引用符で適切にクォートして返します。
     *
     * @param string $value クォートしたい値
     * @return string クォート結果の文字列
     */
    public function quote($value)
    {
        return $this->pdo->quote($value, \PDO::PARAM_STR);
    }

    /**
     * SQL実行準備を行い、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return PdoStatement
     */
    protected function doPrepare($query)
    {
        $statement = $this->pdo->prepare($query);
        if ($statement === false) {
            throw new \RuntimeException(
                sprintf('Invalid query:%s', $query)
            );
        }
        return new PdoStatement($statement);
    }

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return PdoStatement
     */
    protected function doQuery($query)
    {
        $statement = $this->pdo->query($query);
        if ($statement === false) {
            throw new \RuntimeException(
                sprintf('Invalid query:%s', $query)
            );
        }
        return new PdoStatement($statement);
    }

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     * @return boolean
     */
    protected function doExecute($query)
    {
        return $this->pdo->exec($query);
    }

}
