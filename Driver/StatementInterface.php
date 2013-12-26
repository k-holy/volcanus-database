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
interface StatementInterface extends \IteratorAggregate
{

	/**
	 * フェッチ後に実行するコールバックをセットします。
	 *
	 * @param callable コールバック
	 */
	public function setFetchCallback($callback);

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
	public function setFetchMode($mode, $option = null, array $arguments = null);

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @return mixed
	 */
	public function fetch($mode = null);

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 * @param array Statement::FETCH_CLASS の場合のコンストラクタ引数
	 * @return array
	 */
	public function fetchAll($mode = null, $option = null, array $arguments = null);

}
