<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaDataProcessor;

use Volcanus\Database\Driver\StatementInterface;
use Volcanus\Database\Database;
use Volcanus\Database\Table;
use Volcanus\Database\Column;

/**
 * PDOコネクション
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlMetaDataProcessor implements MetaDataProcessorInterface
{

	/**
	 * テーブル情報を取得するクエリを返します。
	 *
	 * @return string SQL
	 */
	public function metaTablesQuery()
	{
		return 'SHOW TABLES;';
	}

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\StatementInterface ステートメント
	 * @return array of Table
	 */
	public function getMetaTables(StatementInterface $statement)
	{
		$statement->setFetchMode(Database::FETCH_NUM);
		$tables = array();
		foreach ($statement as $cols) {
			$table = new Table();
			$table->name = $cols[0];
			$tables[$cols[0]] = $table;
		}
		return $tables;
	}

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	public function metaColumnsQuery($table)
	{
		return sprintf('SHOW FULL COLUMNS FROM %s', $table);
	}

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\StatementInterface ステートメント
	 * @return array of Column
	 */
	public function getMetaColumns(StatementInterface $statement)
	{
		$statement->setFetchMode(Database::FETCH_ASSOC);
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

}
