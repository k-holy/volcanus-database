<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * @return bool 処理に失敗した場合に false を返します。
     */
    public function begin(): bool;

    /**
     * トランザクションをコミットします。
     *
     * @return bool 処理に失敗した場合に false を返します。
     */
    public function commit(): bool;

    /**
     * トランザクションをロールバックします。
     *
     * @return bool 処理に失敗した場合に false を返します。
     */
    public function rollback(): bool;

}
