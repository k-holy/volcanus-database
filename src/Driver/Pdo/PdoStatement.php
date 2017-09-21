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

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var int フェッチモード
     */
    private $fetchMode;

    /**
     * @var callable フェッチ後に実行するコールバック
     */
    private $callback;

    /**
     * コンストラクタ
     *
     * @param \PDOStatement
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
        $this->callback = null;
    }

    /**
     * フェッチ後に実行するコールバックをセットします。
     *
     * @param callable $callback コールバック
     */
    public function setFetchCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                sprintf('CallbackIterator accepts only callable, invalid type:%s',
                    (is_object($callback))
                        ? get_class($callback)
                        : gettype($callback)
                )
            );
        }
        $this->callback = $callback;
    }

    /**
     * プリペアドステートメントを実行します。
     *
     * @param array|\Traversable $parameters パラメータ
     * @return bool
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
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
                $this->statement->bindValue($name, $value, $type);
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
     * @param int $mode フェッチモード定数 (Statement::FETCH_**)
     * @param mixed $option フェッチモードのオプション引数
     * @param array $arguments Statement::FETCH_CLASS の場合のコンストラクタ引数
     * @return $this
     */
    public function setFetchMode($mode, $option = null, array $arguments = null)
    {
        $this->fetchMode = $mode;
        $fetchMode = $this->convertFetchMode($mode);
        switch (func_num_args()) {
            case 3:
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $this->statement->setFetchMode($fetchMode, $option, $arguments);
                break;
            case 2:
                /** @noinspection PhpMethodParametersCountMismatchInspection */
                $this->statement->setFetchMode($fetchMode, $option);
                break;
            case 1:
                $this->statement->setFetchMode($fetchMode);
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
        $result = $this->statement->fetch();
        if (!isset($this->callback) || $result === false) {
            return $result;
        }
        return call_user_func($this->callback, $result);
    }

    /**
     * 結果セットから全ての行を取得して配列で返します。
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->statement->fetchAll();
    }

    /**
     * IteratorAggregate::getIterator()
     *
     * @return \Traversable
     */
    public function getIterator()
    {
        return (isset($this->callback))
            ? new CallbackIterator($this->statement, $this->callback)
            : new \IteratorIterator($this->statement);
    }

    private function convertFetchMode($mode)
    {
        switch ($mode) {
            case Statement::FETCH_ASSOC:
                return \PDO::FETCH_ASSOC;
            case Statement::FETCH_NUM:
                return \PDO::FETCH_NUM;
            case Statement::FETCH_CLASS:
                return \PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE;
        }
        throw new \InvalidArgumentException(
            sprintf('Unsupported fetchMode:%s', $mode)
        );
    }

}
