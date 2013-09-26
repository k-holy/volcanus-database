<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Database;
use Volcanus\Database\Column;
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
		$this->queryBuilder = null;
		if (isset($pdo)) {
			$this->connect($pdo);
		}
		if (isset($metaDataProcessor)) {
			$this->metaDataProcessor = $metaDataProcessor;
		}
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
	 * SQL実行準備を行い、ステートメントオブジェクトを返します。
	 *
	 * @param string SQL
	 * @return PdoStatement
	 */
	public function prepare($query)
	{
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
		return $this->pdo->exec($query);
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
		return $this->metaDataProcessor->getMetaTables(
			new PdoStatement($this->pdo->query(
				$this->metaDataProcessor->metaTablesQuery()
			))
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
		return $this->metaDataProcessor->getMetaColumns(
			new PdoStatement($this->pdo->query(
				$this->metaDataProcessor->metaColumnsQuery($table)
			))
		);
	}

}
