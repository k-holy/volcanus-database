<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

use Volcanus\Database\Dsn;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\MetaData\MetaDataProcessorInterface;
use Volcanus\Database\MetaData\Table;

/**
 * ドライバインタフェース
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractDriver implements DriverInterface
{

    /**
     * @var Dsn|null
     */
    protected ?Dsn $dsn = null;

    /**
     * @var MetaDataProcessorInterface|null
     */
    protected ?MetaDataProcessorInterface $metaDataProcessor = null;

    /**
     * @var string|null LastQuery
     */
    protected ?string $lastQuery = null;

    /**
     * @var string LIKE演算子のエスケープ文字
     */
    protected string $escapeCharacter = '\\';

    /**
     * DSNをセットします。
     *
     * @param Dsn $dsn
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * メタデータプロセッサをセットします。
     *
     * @param MetaDataProcessorInterface $metaDataProcessor
     */
    public function setMetaDataProcessor(MetaDataProcessorInterface $metaDataProcessor)
    {
        $this->metaDataProcessor = $metaDataProcessor;
    }

    /**
     * ドライバに合ったメタデータプロセッサを生成します。
     *
     * @return MetaDataProcessorInterface
     */
    public function createMetaDataProcessor(): MetaDataProcessorInterface
    {
        $driverName = $this->getDriverName();
        if (!isset($driverName)) {
            throw new \RuntimeException(
                'Could not create MetaDataProcessor disconnected.'
            );
        }
        $className = sprintf('\Volcanus\Database\MetaData\%sMetaDataProcessor',
            ucfirst($driverName)
        );
        return new $className();
    }

    /**
     * SQL実行準備を行い、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    public function prepare(string $query): StatementInterface
    {
        $this->lastQuery = $query;
        return $this->doPrepare($query);
    }

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    public function query(string $query): StatementInterface
    {
        $this->lastQuery = $query;
        return $this->doQuery($query);
    }

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     * @return int
     */
    public function execute(string $query): int
    {
        $this->lastQuery = $query;
        return $this->doExecute($query);
    }

    /**
     * 最後に発行(prepare/query/execute)したクエリを返します。
     *
     * @return string
     */
    public function getLastQuery(): string
    {
        return $this->lastQuery;
    }

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @return Table[]
     */
    public function getMetaTables(): array
    {
        if (!isset($this->metaDataProcessor)) {
            throw new \RuntimeException(
                'metaDataProcessor is not set'
            );
        }
        return $this->metaDataProcessor->getMetaTables($this);
    }

    /**
     * 指定テーブルのカラムオブジェクトを配列で返します。
     *
     * @param string $table テーブル名
     * @return Column[]
     */
    public function getMetaColumns(string $table): array
    {
        if (!isset($this->metaDataProcessor)) {
            throw new \RuntimeException(
                'metaDataProcessor is not set'
            );
        }
        return $this->metaDataProcessor->getMetaColumns($this, $table);
    }

    /**
     * LIKE演算子のエスケープ文字をセットします。
     *
     * @param string $char エスケープに使用する文字
     */
    public function setEscapeCharacter(string $char)
    {
        $this->escapeCharacter = $char;
    }

    /**
     * LIKE演算子のパターンとして使用する文字列をエスケープして返します。
     *
     * @param string $pattern パターン文字列
     * @return string エスケープされたパターン文字列
     */
    public function escapeLikePattern(string $pattern): string
    {
        return strtr($pattern, [
            '_' => $this->escapeCharacter . '_',
            '%' => $this->escapeCharacter . '%',
            $this->escapeCharacter => $this->escapeCharacter . $this->escapeCharacter,
        ]);
    }

    /**
     * SQL実行準備を行い、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    abstract protected function doPrepare(string $query): StatementInterface;

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    abstract protected function doQuery(string $query): StatementInterface;

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     * @return int
     */
    abstract protected function doExecute(string $query): int;

}
