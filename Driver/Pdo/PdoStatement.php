<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Database;
use Volcanus\Database\Driver\StatementInterface;

/**
 * PDOステートメント
 *
 * @author k.holy74@gmail.com
 */
class PdoStatement implements StatementInterface, \IteratorAggregate
{

	private $statement;
	private $fetchMode;

	/**
	 * コンストラクタ
	 *
	 * @param PDOStatement
	 */
	public function __construct(\PDOStatement $statement)
	{
		$this->statement = $statement;
		$this->setFetchMode(Database::FETCH_ASSOC);
	}

	/**
	 * プリペアドステートメントを実行します。
	 *
	 * @param array | \Traversable パラメータ
	 * @return bool
	 */
	public function execute($parameters = null)
	{
		if (isset($parameters)) {
			if (!is_array($parameters) && !($parameters instanceof \Traversable)) {
				throw new \InvalidArgumentException(
					sprintf('Parameters accepts an Array or Traversable, invalid type:%s',
						(is_object($parameters))
							? get_class($parameters)
							: gettype($parameters)
					)
				);
			}
			foreach ($parameters as $name => $value) {
				$type = \PDO::PARAM_STR;
				if (is_int($value)) {
					$type = \PDO::PARAM_INT;
				} elseif (is_bool($value)) {
					$type = \PDO::PARAM_BOOL;
				} elseif (is_null($value)) {
					$type = \PDO::PARAM_NULL;
				}
				$this->statement->bindValue(
					(strncmp(':', $name, 1) !== 0) ? sprintf(':%s', $name) : $name,
					$value,
					$type
				);
			}
		}
		try {
			return $this->statement->execute();
		} catch (\PDOException $e) {
			ob_start();
			$this->statement->debugDumpParams();
			$debug = ob_get_contents();
			ob_end_clean();
			throw new \InvalidArgumentException(
				sprintf('execute prepared statement failed. "%s"', $debug)
			);
		}
	}

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (Database::FETCH_**)
	 * @param mix フェッチモードのオプション
	 */
	public function setFetchMode($mode, $value = null)
	{
		switch ($mode) {
		case Database::FETCH_ASSOC:
			$this->fetchMode = $mode;
			$this->statement->setFetchMode(\PDO::FETCH_ASSOC);
			break;
		case Database::FETCH_NUM:
			$this->fetchMode = $mode;
			$this->statement->setFetchMode(\PDO::FETCH_NUM);
			break;
		}
		return $this;
	}

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @return mixed
	 */
	public function fetch()
	{
		return $this->statement->fetch();
	}

	/**
	 * 結果セットから次の行をオブジェクトで取得して返します。
	 *
	 * @param string クラス名
	 * @param array コンストラクタ引数
	 * @return mixed
	 */
	public function fetchObject($class, $arguments = array())
	{
		return $this->statement->fetchObject($class, $arguments);
	}

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param callable コールバック関数
	 * @return array
	 */
	public function fetchAll($function = null)
	{
		if (isset($function)) {
			if (!is_callable($function)) {
				throw new \InvalidArgumentException(
					sprintf('function accepts only callable, invalid type:%s',
						(is_object($function))
							? get_class($function)
							: gettype($function)
					)
				);
			}
			return $this->statement->fetchAll(\PDO::FETCH_FUNC, $function);
		}
		return $this->statement->fetchAll();
	}

	/**
	 * \IteratorAggregate::getIterator()
	 *
	 * @return \Traversable
	 */
	public function getIterator()
	{
		return $this->statement;
	}

}
