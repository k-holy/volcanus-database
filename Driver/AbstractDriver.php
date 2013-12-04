<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

use Volcanus\Database\Driver\DriverInterface;
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
	 * @var Volcanus\Database\Dsn
	 */
	protected $dsn;

	/**
	 * @var Volcanus\Database\MetaData\MetaDataProcessorInterface
	 */
	protected $metaDataProcessor;

	/**
	 * @var string LastQuery
	 */
	protected $lastQuery;

	/**
	 * DSNをセットします。
	 *
	 * @param Volcanus\Database\Dsn
	 */
	public function setDsn(Dsn $dsn)
	{
		$this->dsn = $dsn;
	}

	/**
	 * メタデータプロセッサをセットします。
	 *
	 * @param Volcanus\Database\MetaData\MetaDataProcessorInterface
	 */
	public function setMetaDataProcessor(MetaDataProcessorInterface $metaDataProcessor)
	{
		$this->metaDataProcessor = $metaDataProcessor;
	}

	/**
	 * ドライバに合ったメタデータプロセッサを生成します。
	 *
	 * @return Volcanus\Database\MetaData\MetaDataProcessorInterface
	 */
	public function createMetaDataProcessor()
	{
		$driverName = $this->getDriverName();
		if (!isset($driverName)) {
			throw new \RuntimeException('Could not create MetaDataProcessor disconnected.');
		}
		$className = sprintf('\\Volcanus\\Database\\MetaData\\%sMetaDataProcessor',
			ucfirst($driverName)
		);
		return new $className();
	}

	/**
	 * SQL実行準備を行い、ステートメントオブジェクトを返します。
	 *
	 * @param string SQL
	 * @return PdoStatement
	 */
	public function prepare($query)
	{
		$this->lastQuery = $query;
		return $this->doPrepare($query);
	}

	/**
	 * SQLを実行し、ステートメントオブジェクトを返します。
	 *
	 * @param string SQL
	 * @return PdoStatement
	 */
	public function query($query)
	{
		$this->lastQuery = $query;
		return $this->doQuery($query);
	}

	/**
	 * SQLを実行します。
	 *
	 * @param string SQL
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
	 * @param string テーブル名
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
	 * DBに接続します。
	 *
	 * @param Volcanus\Database\Dsn DSNオブジェクト
	 * @return self
	 */
	abstract public function connect(Dsn $dsn);

	/**
	 * DBとの接続を解放します。
	 *
	 * @return bool
	 */
	abstract public function disconnect();

	/**
	 * DBと接続中かどうかを返します。
	 *
	 * @return bool
	 */
	abstract public function connected();

	/**
	 * ドライバ名を返します。
	 *
	 * @return string ドライバ名
	 */
	abstract public function getDriverName();

	/**
	 * 最後に発生したエラーを返します。
	 *
	 * @return string
	 */
	abstract public function getLastError();

	/**
	 * 直近のinsert操作で生成されたIDを返します。
	 *
	 * @return mixed 実行結果
	 */
	abstract public function lastInsertId();

	/**
	 * 文字列を引用符で適切にクォートして返します。
	 *
	 * @param string クォートしたい値
	 * @return string クォート結果の文字列
	 */
	abstract public function quote($value);

	/**
	 * SQL実行準備を行い、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return StatementInterface
	 */
	abstract protected function doPrepare($query);

	/**
	 * SQLを実行し、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return StatementInterface
	 */
	abstract protected function doQuery($query);

	/**
	 * SQLを実行します。
	 *
	 * @string SQL
	 */
	abstract protected function doExecute($query);

}
