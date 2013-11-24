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
 * PostgreSQL メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class PostgresqlMetaDataProcessor implements MetaDataProcessorInterface
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
		return <<<SQL
SELECT
    tablename
   ,'T'
FROM
    pg_tables
WHERE
    tablename NOT LIKE 'pg\_%'
AND tablename NOT IN ('sql_features', 'sql_implementation_info', 'sql_languages', 'sql_packages', 'sql_sizing', 'sql_sizing_profiles')
UNION
SELECT
    viewname
   ,'V'
FROM
    pg_views
WHERE
    viewname NOT LIKE 'pg\_%'
SQL
		;
	}

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function metaColumnsQuery($table)
	{
		return sprintf(<<<SQL
SELECT
    a.attname
   ,t.typname
   ,a.attlen
   ,a.atttypmod
   ,a.attnotnull
   ,a.atthasdef
   ,a.attnum
   ,d.description
FROM
    pg_attribute a
LEFT JOIN
    pg_class c
ON
    a.attrelid = c.oid
LEFT JOIN
    pg_type t
ON
    a.atttypid = t.oid
LEFT JOIN
    pg_description d
ON
    a.attrelid = d.objoid
AND a.attnum = d.objsubid
WHERE
    c.relkind IN ('r','v')
AND (c.relname = '%s' OR c.relname = lower('%s'))
AND a.attname NOT LIKE '....%%'
AND a.attnum > 0
ORDER BY
    a.attnum
SQL
		, $table, $table);
	}

	/**
	 * 指定テーブルのキー情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function metaKeyQuery($table)
	{
		return sprintf(<<<SQL
SELECT
    ic.relname AS index_name
   ,a.attname AS column_name
   ,i.indisunique AS unique_key
   ,i.indisprimary AS primary_key
FROM
    pg_class bc
   ,pg_class ic
   ,pg_index i
   ,pg_attribute a
WHERE
    bc.oid = i.indrelid
AND ic.oid = i.indexrelid
AND (
    i.indkey[0] = a.attnum
 OR i.indkey[1] = a.attnum
 OR i.indkey[2] = a.attnum
 OR i.indkey[3] = a.attnum
 OR i.indkey[4] = a.attnum
 OR i.indkey[5] = a.attnum
 OR i.indkey[6] = a.attnum
 OR i.indkey[7] = a.attnum
)
AND a.attrelid = bc.oid
AND bc.relname = '%s'
SQL
		, $table);
	}

	/**
	 * 指定テーブルのデフォルト情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	private function metaDefaultQuery($table)
	{
		return sprintf(<<<SQL
SELECT
    d.adnum AS num
   ,d.adsrc AS def
FROM
    pg_attrdef d
   ,pg_class c
WHERE
    d.adrelid=c.oid
AND c.relname='%s'
ORDER BY
    d.adnum
SQL
		, $table);
	}

}
