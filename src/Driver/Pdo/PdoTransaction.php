<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * @var \PDO
     */
    private \PDO $pdo;

    /**
     * コンストラクタ
     *
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * トランザクションを開始します。
     *
     * @return bool 処理に失敗した場合に false を返します。
     * @throws \RuntimeException
     */
    public function begin(): bool
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
     * @return bool 処理に失敗した場合に false を返します。
     * @throws \RuntimeException
     */
    public function commit(): bool
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
     * @return bool 処理に失敗した場合に false を返します。
     * @throws \RuntimeException
     */
    public function rollback(): bool
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
