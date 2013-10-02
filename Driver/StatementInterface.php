<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

/**
 * ステートメントインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface StatementInterface
{

	/**
	 * プリペアドステートメントを実行します。
	 *
	 * @param array | \Traversable パラメータ
	 */
	public function execute($parameters = null);

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 */
	public function setFetchMode($mode, $option = null);

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @return mixed
	 */
	public function fetch($mode = null);

	/**
	 * 結果セットから次の行をオブジェクトのプロパティに取得して返します。
	 *
	 * 第2引数が TRUE の場合は 結果セットの値をオブジェクトのプロパティにセットする前に、プロパティの存在を確認します。
	 * ※マジックメソッド __set() を利用する場合は FALSE に設定してください。
	 *
	 * @param object オブジェクト
	 * @param bool プロパティの存在をチェックするかどうか
	 * @return mixed
	 */
	public function fetchInto($object, $checkPropertyExists = true);

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param callable コールバック関数
	 * @return array
	 */
	public function fetchAll($function = null);

}
