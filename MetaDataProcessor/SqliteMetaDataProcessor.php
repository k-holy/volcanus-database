<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaDataProcessor;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Statement;
use Volcanus\Database\Table;
use Volcanus\Database\Column;

/**
 * SQLite メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteMetaDataProcessor implements MetaDataProcessorInterface
{

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @return array of Table
	 */
	public function getMetaTables(DriverInterface $driver)
	{
		$statement = $driver->query($this->metaTablesQuery());
		$statement->setFetchMode(Statement::FETCH_NUM);
		$tables = array();
		foreach ($statement as $cols) {
			$table = new Table();
			$table->name = $cols[0];
			$tables[$cols[0]] = $table;
		}
		return $tables;
	}

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @param string テーブル名
	 * @return array of Column
	 */
	public function getMetaColumns(DriverInterface $driver, $table)
	{
		$statement = $driver->query($this->metaColumnsQuery($table));
		$statement->setFetchMode(Statement::FETCH_NUM);
		$columns = array();
		foreach ($statement as $cols) {
			$column = new Column();
			$column->name = $cols[1];
			if (preg_match("/^(.+)\((\d+),(\d+)/", $cols[2], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
				$column->scale = is_numeric($matches[3]) ? $matches[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $cols[2], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
			} else {
				$column->type = $cols[2];
			}
			$column->notNull = (bool)$cols[3];
			$column->primaryKey = (bool)$cols[5];
			$column->autoIncrement = ($column->primaryKey && strcasecmp($column->type, 'INTEGER') === 0);
			$column->binary = (strcasecmp($column->type, 'BLOB') === 0);
			if (!$column->binary && strcmp($cols[4], '') !== 0 && strcasecmp($cols[4], 'NULL') !== 0) {
				$column->default = $cols[4];
			}
			$columns[$cols[1]] = $column;
		}
		return $columns;
	}

	/**
	 * テーブル情報を取得するクエリを返します。
	 *
	 * @return string SQL
	 */
	private function metaTablesQuery()
	{
		return "SELECT name FROM sqlite_master WHERE type='table'";
	}

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function metaColumnsQuery($table)
	{
		return sprintf('PRAGMA TABLE_INFO(%s);', $table);
	}

}
