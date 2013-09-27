<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

/**
 * ドライバインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface DriverInterface
{

	/**
	 * DBに接続します。
	 *
	 * @param mixed DSN、コネクションリソース等 (Driverクラス依存)
	 * @return self
	 */
	public function connect($resource);

	/**
	 * DBとの接続を解放します。
	 *
	 * @return bool
	 */
	public function disconnect();

	/**
	 * DBと接続中かどうかを返します。
	 *
	 * @return bool
	 */
	public function connected();

	/**
	 * SQL実行準備を行い、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return StatementInterface
	 */
	public function prepare($query);

	/**
	 * SQLを実行し、ステートメントオブジェクトを返します。
	 *
	 * @string SQL
	 * @return StatementInterface
	 */
	public function query($query);

	/**
	 * SQLを実行します。
	 *
	 * @string SQL
	 */
	public function execute($query);

	/**
	 * 最後に発行(prepare/query/execute)したクエリを返します。
	 *
	 * @return string
	 */
	public function getLastQuery();

	/**
	 * 最後に発生したエラーを返します。
	 *
	 * @return string
	 */
	public function getLastError();

	/**
	 * 直近のinsert操作で生成されたIDを返します。
	 *
	 * @return mixed 実行結果
	 */
	public function lastInsertId();

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @return array of Table
	 */
	public function getMetaTables();

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param string テーブル名
	 * @return array of Column
	 */
	public function getMetaColumns($table);

	/**
	 * 文字列を引用符で適切にクォートして返します。
	 *
	 * @param string クォートしたい値
	 * @return string クォート結果の文字列
	 */
	public function quote($value);

}
