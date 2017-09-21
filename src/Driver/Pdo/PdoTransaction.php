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
     * @throws RuntimeException
     */
    public function begin()
    {
        try {
            return $this->pdo->beginTransaction();
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                sprintf('Failed to begin transaction. "%s"', $e->getMessage())
            );
        }
    }

    /**
     * トランザクションをコミットします。
     *
     * @return boolean 処理に失敗した場合に false を返します。
     * @throws RuntimeException
     */
    public function commit()
    {
        try {
            return $this->pdo->commit();
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                sprintf('Failed to commit transaction. "%s"', $e->getMessage())
            );
        }
    }

    /**
     * トランザクションをロールバックします。
     *
     * @return boolean 処理に失敗した場合に false を返します。
     * @throws RuntimeException
     */
    public function rollback()
    {
        try {
            return $this->pdo->rollBack();
        } catch (\PDOException $e) {
            throw new \RuntimeException(
                sprintf('Failed to rollback transaction. "%s"', $e->getMessage())
            );
        }
    }

}
