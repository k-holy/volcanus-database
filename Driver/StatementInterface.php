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
	 * @param array | Traversable パラメータ
	 */
	public function execute($parameters = null);

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 * @param array Statement::FETCH_CLASS の場合のコンストラクタ引数
	 */
	public function setFetchMode($mode, $option = null, array $arguments = array());

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @return mixed
	 */
	public function fetch($mode = null);

	/**
	 * 指定したクラスのインスタンスを生成して結果セットから次の行をプロパティに取得して返します。
	 *
	 * 第3引数に TRUE を指定した場合、オブジェクトに同名のプロパティが存在する時のみ結果セットの値を取得します。
	 * マジックメソッド __set() を利用する場合は FALSE に設定してください。
	 *
	 * @param string クラス名
	 * @param array コンストラクタ引数
	 * @param bool プロパティの存在をチェックするかどうか
	 * @return mixed
	 */
	public function fetchInstanceOf($className, array $arguments = null, $checkPropertyExists = true);

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 * @param array Statement::FETCH_CLASS の場合のコンストラクタ引数
	 * @return array
	 */
	public function fetchAll($mode = null, $option = null, array $arguments = array());

}
