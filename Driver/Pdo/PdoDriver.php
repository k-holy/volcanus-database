<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\MetaDataProcessor\MetaDataProcessorInterface;

/**
 * PDOコネクション
 *
 * @author k_horii@rikcorp.jp
 */
class PdoDriver implements DriverInterface
{

	/**
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * @var string LastQuery
	 */
	private $lastQuery;

	/**
	 * @var \Volcanus\Database\MetaDataProcessorInterface
	 */
	private $metaDataProcessor;

	/**
	 * コンストラクタ
	 *
	 * @param \PDO
	 * @param \Volcanus\Database\MetaDataProcessorInterface
	 */
	public function __construct(\PDO $pdo = null, MetaDataProcessorInterface $metaDataProcessor = null)
	{
		$this->pdo = null;
		$this->lastQuery = null;
		if (isset($pdo)) {
			$this->connect($pdo);
			if (!isset($metaDataProcessor)) {
				$metaDataProcessor = $this->createMetaDataProcessor();
			}
		}
		if (isset($metaDataProcessor)) {
			$this->setMetaDataProcessor($metaDataProcessor);
		}
	}

	/**
	 * メタデータプロセッサをセットします。
	 *
	 * @param \Volcanus\Database\MetaDataProcessorInterface
	 */
	public function setMetaDataProcessor(MetaDataProcessorInterface $metaDataProcessor)
	{
		$this->metaDataProcessor = $metaDataProcessor;
	}

	/**
	 * DBに接続します。
	 *
	 * @param \PDO
	 * @return self
	 */
	public function connect($pdo)
	{
		if (!($pdo instanceof \PDO)) {
			throw new \InvalidArgumentException(
				sprintf('The argument is not PDO instance. type:%s', gettype($pdo))
			);
		}
		$this->pdo = $pdo;
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
	 * @return string ドライバ名
	 */
	public function getDriverName()
	{
		if (isset($this->pdo)) {
			return $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
		}
		return null;
	}

	/**
	 * ドライバに合ったメタデータプロセッサを生成します。
	 *
	 * @return Volcanus\Database\MetaDataProcessorInterface
	 */
	public function createMetaDataProcessor()
	{
		$driverName = $this->getDriverName();
		if (!isset($driverName)) {
			throw new \RuntimeException('Could not create MetaDataProcessor disconnected.');
		}
		$className = sprintf('\\Volcanus\\Database\\MetaDataProcessor\\%sMetaDataProcessor',
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
		return new PdoStatement($this->pdo->prepare($query));
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
		return new PdoStatement($this->pdo->query($query));
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
		return $this->pdo->exec($query);
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
	 * 最後に発生したエラーを返します。
	 *
	 * @return string
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
		return $this->metaDataProcessor->getMetaTables(
			$this->query(
				$this->metaDataProcessor->metaTablesQuery()
			)
		);
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
		return $this->metaDataProcessor->getMetaColumns(
			$this->query(
				$this->metaDataProcessor->metaColumnsQuery($table)
			)
		);
	}

	/**
	 * 文字列を引用符で適切にクォートして返します。
	 *
	 * @param string クォートしたい値
	 * @return string クォート結果の文字列
	 */
	public function quote($value)
	{
		return $this->pdo->quote($value, \PDO::PARAM_STR);
	}

}
