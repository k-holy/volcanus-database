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
 * MySQL メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlMetaDataProcessor extends AbstractMetaDataProcessor
{

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @return array of Table
	 */
	protected function doGetMetaTables(DriverInterface $driver)
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
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @param string テーブル名
	 * @return array of Column
	 */
	protected function doGetMetaColumns(DriverInterface $driver, $table)
	{
		$statement = $driver->query($this->metaColumnsQuery($table));
		$statement->setFetchMode(Statement::FETCH_ASSOC);
		$columns = array();
		foreach ($statement as $cols) {
			$column = new Column();
			$column->name = $cols['Field'];
			if (preg_match("/^(.+)\((\d+),(\d+)/", $cols['Type'], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
				$column->scale = is_numeric($matches[3]) ? $matches[3] : -1;
			} elseif (preg_match("/^(.+)\((\d+)/", $cols['Type'], $matches)) {
				$column->type = $matches[1];
				$column->maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
			} elseif (preg_match("/^(enum)\((.*)\)$/i", $cols['Type'], $matches)) {
				$column->type = $matches[1];
				$zlen = max(array_map('strlen', explode(',', $matches[2]))) - 2;
				$column->maxLength = ($zlen > 0) ? $zlen : 1;
			} else {
				$column->type = $cols['Type'];
			}
			$column->notNull = ($cols['Null'] !== 'YES');
			$column->primaryKey = ($cols['Key'] === 'PRI');
			$column->uniqueKey = ($cols['Key'] === 'UNI');
			$column->autoIncrement  = (strpos($cols['Extra'], 'auto_increment') !== false);
			$column->binary = (strpos($cols['Type'],'blob') !== false);
			if (!$column->binary && strcmp($cols['Default'], '') !== 0 && strcasecmp($cols['Default'], 'NULL') !== 0) {
				$column->default = $cols['Default'];
			}
			$column->comment = (isset($cols['Comment']) && strcmp($cols['Comment'], '') != 0) ? $cols['Comment'] : null;
			$columns[$cols['Field']] = $column;
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
		return 'SHOW TABLES;';
	}

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function metaColumnsQuery($table)
	{
		return sprintf('SHOW FULL COLUMNS FROM %s', $table);
	}

}
