<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Driver\AbstractDriver;
use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Driver\StatementInterface;
use Volcanus\Database\Dsn;
use Volcanus\Database\MetaData\MetaDataProcessorInterface;

/**
 * PDOコネクション
 *
 * @property Dsn $dsn
 * @property MetaDataProcessorInterface $metaDataProcessor
 * @property string $lastQuery
 * @property string $escapeCharacter
 *
 * @author k_horii@rikcorp.jp
 */
class PdoDriver extends AbstractDriver
{

    /**
     * @var \PDO|null
     */
    private ?\PDO $pdo = null;

    /**
     * コンストラクタ
     *
     * @param \PDO|null $pdo
     * @param MetaDataProcessorInterface|null $metaDataProcessor
     */
    public function __construct(\PDO $pdo = null, MetaDataProcessorInterface $metaDataProcessor = null)
    {
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
     * @param Dsn $dsn
     * @return static
     */
    public static function createFromDsn(Dsn $dsn): self
    {
        $driver = new static(PdoFactory::createFromDsn($dsn));
        $driver->setDsn($dsn);
        return $driver;
    }

    /**
     * DBに接続します。
     *
     * @param Dsn|null $dsn DSNオブジェクト
     * @return $this
     */
    public function connect(Dsn $dsn = null): DriverInterface
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
    public function disconnect(): bool
    {
        $this->pdo = null;
        return true;
    }

    /**
     * DBと接続中かどうかを返します。
     *
     * @return bool
     */
    public function connected(): bool
    {
        return isset($this->pdo);
    }

    /**
     * ドライバ名を返します。
     *
     * @return string|null ドライバ名
     */
    public function getDriverName(): ?string
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
    public function getLastError(): ?string
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
        $errors = $this->pdo->errorInfo();
        return (isset($errors[2])) ? $errors[2] : null;
    }

    /**
     * 直近のinsert操作で生成されたIDを返します。
     *
     * @return false|string 実行結果
     */
    public function lastInsertId(): bool|string
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
        return $this->pdo->lastInsertId();
    }

    /**
     * 文字列を引用符で適切にクォートして返します。
     *
     * @param string $value クォートしたい値
     * @return string クォート結果の文字列
     */
    public function quote(string $value): string
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
        return $this->pdo->quote($value, \PDO::PARAM_STR);
    }

    /**
     * SQL実行準備を行い、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return PdoStatement
     */
    protected function doPrepare(string $query): StatementInterface
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
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
    protected function doQuery(string $query): StatementInterface
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
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
     * @return int
     */
    protected function doExecute(string $query): int
    {
        if ($this->pdo === null) {
            throw new \RuntimeException(
                'PDO not connected.'
            );
        }
        return $this->pdo->exec($query);
    }

}
