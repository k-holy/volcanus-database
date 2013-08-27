<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaDataProcessor;

use Volcanus\Database\Driver\StatementInterface;

/**
 * メタデータプロセッサインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface MetaDataProcessorInterface
{

	/**
	 * テーブル情報を取得するクエリを返します。
	 *
	 * @return string SQL
	 */
	public function metaTablesQuery();

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\StatementInterface ステートメント
	 * @return array of Table
	 */
	public function getMetaTables(StatementInterface $statement);

	/**
	 * 指定テーブルのカラム情報を取得するクエリを返します。
	 *
	 * @param string テーブル名
	 * @return string SQL
	 */
	public function metaColumnsQuery($table);

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\StatementInterface ステートメント
	 * @return array of Column
	 */
	public function getMetaColumns(StatementInterface $statement);

}
