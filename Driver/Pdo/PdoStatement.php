<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Statement;
use Volcanus\Database\Driver\StatementInterface;
use Volcanus\Database\CallbackIterator;

/**
 * PDOステートメント
 *
 * @author k.holy74@gmail.com
 */
class PdoStatement implements StatementInterface
{

	private $statement;
	private $fetchMode;
	private $function;

	/**
	 * コンストラクタ
	 *
	 * @param PDOStatement
	 */
	public function __construct(\PDOStatement $statement)
	{
		$this->statement = $statement;
		$this->setFetchMode(Statement::FETCH_ASSOC);
		$this->function = null;
	}

	/**
	 * プリペアドステートメントを実行します。
	 *
	 * @param array | Traversable パラメータ
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
			throw new \RuntimeException(
				sprintf('execute prepared statement failed. "%s"', $debug)
			);
		}
	}

	/**
	 * このステートメントのデフォルトのフェッチモードを設定します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 * @param array Statement::FETCH_CLASS の場合のコンストラクタ引数
	 */
	public function setFetchMode($mode, $option = null, array $arguments = array())
	{
		switch ($mode) {
		case Statement::FETCH_ASSOC:
			$this->fetchMode = $mode;
			$this->statement->setFetchMode(\PDO::FETCH_ASSOC);
			break;
		case Statement::FETCH_NUM:
			$this->fetchMode = $mode;
			$this->statement->setFetchMode(\PDO::FETCH_NUM);
			break;
		case Statement::FETCH_CLASS:
			$this->fetchMode = $mode;
			if (!class_exists($option, true)) {
				throw new \InvalidArgumentException(
					sprintf('Statement::FETCH_CLASS accepts only className, unknown className:%s',
						$option
					)
				);
			}
			$this->statement->setFetchMode(\PDO::FETCH_CLASS, $option, $arguments);
			break;
		case Statement::FETCH_FUNC:
			$this->fetchMode = $mode;
			if (!is_callable($option)) {
				throw new \InvalidArgumentException(
					sprintf('Statement::FETCH_FUNC accepts only callable, invalid type:%s',
						(is_object($option))
							? get_class($option)
							: gettype($option)
					)
				);
			}
			$this->function = $option;
			break;
		default:
			throw new \InvalidArgumentException(
				sprintf('Unsupported fetchMode:%s', $mode)
			);
			break;
		}
		return $this;
	}

	/**
	 * 結果セットから次の行を取得して返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @return mixed
	 */
	public function fetch($mode = null)
	{
		if (is_null($mode)) {
			$mode = $this->fetchMode;
		}
		switch ($mode) {
		case Statement::FETCH_ASSOC:
			return $this->statement->fetch(\PDO::FETCH_ASSOC);
		case Statement::FETCH_NUM:
			return $this->statement->fetch(\PDO::FETCH_NUM);
		case Statement::FETCH_CLASS:
			return $this->statement->fetch(\PDO::FETCH_CLASS);
		case Statement::FETCH_FUNC:
			$result = $this->statement->fetch(\PDO::FETCH_NUM);
			if (!is_array($result)) {
				return false;
			}
			return call_user_func_array($this->function, $result);
		}
		throw new \InvalidArgumentException(
			sprintf('Unsupported fetchMode:%s', $mode)
		);
	}

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
	public function fetchInstanceOf($className, array $arguments = null, $checkPropertyExists = true)
	{
		if (isset($arguments) && count($arguments) >= 1) {
			$ref = new \ReflectionClass($className);
			$object = $ref->newInstanceArgs($arguments);
		} else {
			$object = new $className();
		}
		$columns = $this->statement->fetch(\PDO::FETCH_ASSOC);
		if (!is_array($columns)) {
			return false;
		}
		foreach ($columns as $name => $value) {
			if (!$checkPropertyExists || property_exists($object, $name)) {
				$object->$name = $value;
			}
		}
		return $object;
	}

	/**
	 * 結果セットから全ての行を取得して配列で返します。
	 *
	 * @param int フェッチモード定数 (Statement::FETCH_**)
	 * @param mixed フェッチモードのオプション引数
	 * @param array Statement::FETCH_CLASS の場合のコンストラクタ引数
	 * @return array
	 */
	public function fetchAll($mode = null, $option = null, array $arguments = array())
	{
		if (is_null($mode)) {
			$mode = $this->fetchMode;
		}
		switch ($mode) {
		case Statement::FETCH_ASSOC:
			return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
		case Statement::FETCH_NUM:
			return $this->statement->fetchAll(\PDO::FETCH_NUM);
		case Statement::FETCH_CLASS:
			if (!class_exists($option, true)) {
				throw new \InvalidArgumentException(
					sprintf('Statement::FETCH_CLASS accepts only className, unknown className:%s',
						$option
					)
				);
			}
			return $this->statement->fetchAll(\PDO::FETCH_CLASS, $option, $arguments);
		case Statement::FETCH_FUNC:
			if (!is_callable($option)) {
				throw new \InvalidArgumentException(
					sprintf('Statement::FETCH_FUNC accepts only callable, invalid type:%s',
						(is_object($option))
							? get_class($option)
							: gettype($option)
					)
				);
			}
			return $this->statement->fetchAll(\PDO::FETCH_FUNC, $option);
		}
		throw new \InvalidArgumentException(
			sprintf('Unsupported fetchMode:%s', $mode)
		);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \Traversable
	 */
	public function getIterator()
	{
		if ($this->fetchMode === Statement::FETCH_FUNC) {
			return new CallbackIterator($this->statement, $this->function);
		}
		return $this->statement;
	}

}
