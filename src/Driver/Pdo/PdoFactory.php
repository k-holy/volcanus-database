<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver\Pdo;

use Volcanus\Database\Dsn;

/**
 * Factory for PDO
 *
 * @author k.holy74@gmail.com
 */
class PdoFactory
{

    /**
     * 指定されたPDOドライバを生成して返します。
     * エラーモード定数が指定されていなければ、強制的に ERRMODE_EXCEPTION に設定します。
     *
     * @param string $dsn PDOスタイルのDSN文字列
     * @param string|null $username ユーザー名
     * @param string|null $password パスワード
     * @param array $options 接続オプションの配列
     * @return \PDO
     */
    public static function create(string $dsn, string $username = null, string $password = null, array $options = []): \PDO
    {

        if (!isset($options[\PDO::ATTR_ERRMODE])) {
            $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;
        }

        try {
            $pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \InvalidArgumentException(
                sprintf('Invalid DSN:"%s"', $dsn), 0, $e
            );
        }

        return $pdo;
    }

    /**
     * DSNオブジェクトからPDOドライバを生成して返します。
     *
     * @param Dsn $dsn DSNオブジェクト
     * @param array $driverOptions ドライバ固有の接続オプションを指定するキー=> 値の配列
     * @return \PDO
     */
    public static function createFromDsn(Dsn $dsn, array $driverOptions = []): \PDO
    {
        $options = (isset($dsn->options)) ? $dsn->options : [];
        if (!empty($driverOptions)) {
            $options = $driverOptions + $options;
        }
        return static::create(
            $dsn->toPdo(),
            $dsn->username,
            $dsn->password,
            $options
        );
    }

}
