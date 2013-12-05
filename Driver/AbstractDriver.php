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
abstract class AbstractDriver
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
