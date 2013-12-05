<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Statement;

/**
 * SQLite メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteMetaDataProcessor extends AbstractMetaDataProcessor
{

	/**
	 * コンストラクタ
	 *
	 * @param array | Traversable
	 */
	public function __construct(CacheProcessorInterface $cacheProcessor = null)
	{
		if ($cacheProcessor !== null) {
			$this->setCacheProcessor($cacheProcessor);
		}
	}

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @return array of Table
	 */
	protected function doGetMetaTables(DriverInterface $driver)
	{
		$tableListStatement = $driver->query($this->tableList());
		$tableListStatement->setFetchMode(Statement::FETCH_NUM);
		$tables = array();
		foreach ($tableListStatement as $cols) {
			$table = new Table();
			$table->name = $cols[0];
			$tables[$cols[0]] = $table;
		}
		return $tables;
	}

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @param string テーブル名
	 * @return array of Column
	 */
	protected function doGetMetaColumns(DriverInterface $driver, $table)
	{
		$indexListStatement = $driver->query($this->indexListOf($table));
		$indexListStatement->setFetchMode(Statement::FETCH_ASSOC);
		$indexes = array();
		foreach ($indexListStatement as $cols) {
			$indexInfoStatement = $driver->query($this->indexInfoOf($cols['name']));
			$indexInfo = $indexInfoStatement->fetch(Statement::FETCH_ASSOC);
			$indexes[$indexInfo['name']] = $cols;
		}

		$tableInfoStatement = $driver->query($this->tableInfoOf($table));
		$tableInfoStatement->setFetchMode(Statement::FETCH_ASSOC);
		$columns = array();
		foreach ($tableInfoStatement as $cols) {
			$column = new Column();
			$column->name = $cols['name'];
			if (preg_match("/^(.+)\((\d+),(\d+)/", $cols['type'], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
				$column->scale = is_numeric($matches[3]) ? $matches[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $cols['type'], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
			} else {
				$column->type = $cols['type'];
			}
			$column->notNull = (bool)$cols['notnull'];
			$column->primaryKey = (bool)$cols['pk'];
			$column->uniqueKey = (array_key_exists($cols['name'], $indexes) && $indexes[$cols['name']]['unique'] === '1');
			$column->autoIncrement = ($column->primaryKey && strcasecmp($column->type, 'INTEGER') === 0);
			$column->binary = (strcasecmp($column->type, 'BLOB') === 0);
			if (!$column->binary && strcmp($cols['dflt_value'], '') !== 0 && strcasecmp($cols['dflt_value'], 'NULL') !== 0) {
				$column->default = $cols['dflt_value'];
			}
			$column->comment = null;
			$columns[$cols['name']] = $column;
		}

		return $columns;
	}

	/**
	 * テーブル情報を取得するクエリを返します。
	 *
	 * @return string SQL
	 */
	private function tableList()
	{
		return "SELECT name FROM sqlite_master WHERE type='table'";
	}

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function tableInfoOf($table)
	{
		return sprintf('PRAGMA table_info(%s);', $table);
	}

	/**
	 * 指定テーブルのインデックス情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function indexListOf($table)
	{
		return sprintf('PRAGMA index_list(%s);', $table);
	}

	/**
	 * 指定インデックスの情報を取得するクエリを返します。
	 *
	 * @param string インデックス名
	 * @return string SQL
	 */
	private function indexInfoOf($name)
	{
		return sprintf('PRAGMA index_info(%s);', $name);
	}

}
