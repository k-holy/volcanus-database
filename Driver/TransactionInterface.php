<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

/**
 * トランザクションインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface TransactionInterface
{

	/**
	 * トランザクションを開始します。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function begin();

	/**
	 * トランザクションをコミットします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function commit();

	/**
	 * トランザクションをロールバックします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function rollback();

}
