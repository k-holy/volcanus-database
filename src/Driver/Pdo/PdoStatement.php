<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\CallbackIterator;
use Volcanus\Database\Statement;
use Volcanus\Database\Driver\StatementInterface;

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
    private \PDOStatement $statement;

    /**
     * @var int フェッチモード
     */
    private int $fetchMode;

    /**
     * @var callable|null フェッチ後に実行するコールバック
     */
    private $callback;

    /**
     * コンストラクタ
     *
     * @param \PDOStatement $statement
     */
    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
        $this->fetchMode = Statement::FETCH_ASSOC;
        $this->callback = null;
    }

    /**
     * フェッチ後に実行するコールバックをセットします。
     *
     * @param callable $callback コールバック
     */
    public function setFetchCallback(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * プリペアドステートメントを実行します。
     *
     * @param iterable|null $parameters パラメータ
     * @return bool
     */
    public function execute(iterable $parameters = null): bool
    {
        if (isset($parameters)) {
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
     * @param mixed|null $option フェッチモードのオプション引数
     * @param array|null $arguments Statement::FETCH_CLASS の場合のコンストラクタ引数
     * @return $this
     */
    public function setFetchMode(int $mode, mixed $option = null, array $arguments = null): PdoStatement
    {
        $this->fetchMode = $mode;
        $fetchMode = $this->convertFetchMode($mode);
        switch (func_num_args()) {
            case 3:
                $this->statement->setFetchMode($fetchMode, $option, $arguments);
                break;
            case 2:
                $this->statement->setFetchMode($fetchMode, $option);
                break;
            case 1:
                $this->statement->setFetchMode($fetchMode);
                break;
        }
        return $this;
    }

    /**
     * 現在のデフォルトのフェッチモードを返します。
     *
     * @return int
     */
    public function getFetchMode(): int
    {
        return $this->fetchMode;
    }

    /**
     * 結果セットから次の行を取得して返します。
     *
     * @return mixed
     */
    public function fetch(): mixed
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
    public function fetchAll(): array
    {
        return $this->statement->fetchAll();
    }

    /**
     * IteratorAggregate::getIterator()
     *
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return (isset($this->callback))
            ? new CallbackIterator($this->statement, $this->callback)
            : new \IteratorIterator($this->statement);
    }

    private function convertFetchMode($mode): int
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
