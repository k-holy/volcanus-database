<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Driver\TransactionInterface;

/**
 * PDOトランザクション
 *
 * @author k.holy74@gmail.com
 */
class PdoTransaction implements TransactionInterface
{

	/**
	 * @var PDO
	 */
	private $pdo;

	/**
	 * コンストラクタ
	 *
	 * @param PDO
	 */
	public function __construct(\PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * トランザクションを開始します。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function begin()
	{
		try {
			if ($this->pdo->beginTransaction()) {
				return true;
			}
		} catch (\PDOException $e) {
		}
		throw new \RuntimeException('Failed to begin transaction');
	}

	/**
	 * トランザクションをコミットします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function commit()
	{
		try {
			if ($this->pdo->commit()) {
				return true;
			}
		} catch (\PDOException $e) {
		}
		throw new \RuntimeException('Failed to commit transaction');
	}

	/**
	 * トランザクションをロールバックします。
	 *
	 * @return boolean 処理に失敗した場合に false を返します。
	 */
	public function rollback()
	{
		try {
			if ($this->pdo->rollBack()) {
				return true;
			}
		} catch (\PDOException $e) {
		}
		throw new \RuntimeException('Failed to rollback transaction');
	}

}
