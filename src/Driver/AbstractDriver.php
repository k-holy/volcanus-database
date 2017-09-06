<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

use Volcanus\Database\Dsn;
use Volcanus\Database\MetaData\MetaDataProcessorInterface;

/**
 * ドライバインタフェース
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractDriver implements DriverInterface
{

    /**
     * @var \Volcanus\Database\Dsn
     */
    protected $dsn;

    /**
     * @var \Volcanus\Database\MetaData\MetaDataProcessorInterface
     */
    protected $metaDataProcessor;

    /**
     * @var string LastQuery
     */
    protected $lastQuery;

    /**
     * @var string LIKE演算子のエスケープ文字
     */
    protected $escapeCharacter = '\\';

    /**
     * DSNをセットします。
     *
     * @param \Volcanus\Database\Dsn $dsn
     */
    public function setDsn(Dsn $dsn)
    {
        $this->dsn = $dsn;
    }

    /**
     * メタデータプロセッサをセットします。
     *
     * @param \Volcanus\Database\MetaData\MetaDataProcessorInterface $metaDataProcessor
     */
    public function setMetaDataProcessor(MetaDataProcessorInterface $metaDataProcessor)
    {
        $this->metaDataProcessor = $metaDataProcessor;
    }

    /**
     * ドライバに合ったメタデータプロセッサを生成します。
     *
     * @return \Volcanus\Database\MetaData\MetaDataProcessorInterface
     */
    public function createMetaDataProcessor()
    {
        $driverName = $this->getDriverName();
        if (!isset($driverName)) {
            throw new \RuntimeException('Could not create MetaDataProcessor disconnected.');
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
     * @return \Volcanus\Database\Driver\StatementInterface
     */
    public function prepare($query)
    {
        $this->lastQuery = $query;
        return $this->doPrepare($query);
    }

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return \Volcanus\Database\Driver\StatementInterface
     */
    public function query($query)
    {
        $this->lastQuery = $query;
        return $this->doQuery($query);
    }

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     * @retrun boolean
     */
    public function execute($query)
    {
        $this->lastQuery = $query;
        return $this->doExecute($query);
    }

    /**
     * 最後に発行(prepare/query/execute)したクエリを返します。
     *
     * @return string
     */
    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @return array of Table
     */
    public function getMetaTables()
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
     * @return array of Column
     */
    public function getMetaColumns($table)
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
    public function setEscapeCharacter($char)
    {
        $this->escapeCharacter = $char;
    }

    /**
     * LIKE演算子のパターンとして使用する文字列をエスケープして返します。
     *
     * @param string $pattern パターン文字列
     * @return string エスケープされたパターン文字列
     */
    public function escapeLikePattern($pattern)
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
     * @return \Volcanus\Database\Driver\StatementInterface
     */
    abstract protected function doPrepare($query);

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return \Volcanus\Database\Driver\StatementInterface
     */
    abstract protected function doQuery($query);

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     */
    abstract protected function doExecute($query);

}
