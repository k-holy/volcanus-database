<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * DSNパーサー
 *
 * @author k.holy74@gmail.com
 */
class DsnParser
{

    /**
     * @var array of DSN attributes
     */
    private $attributes;

    /**
     * コンストラクタ
     *
     * @param string $dsn DSN文字列
     */
    public function __construct($dsn = null)
    {
        $this->initialize($dsn);
    }

    /**
     * オブジェクトを初期化します。
     *
     * @param string $dsn DSN文字列
     * @return $this
     */
    public function initialize($dsn = null)
    {
        $this->attributes = [
            'driver' => null,
            'username' => null,
            'password' => null,
            'hostname' => null,
            'port' => null,
            'database' => null,
            'options' => null,
        ];

        if (isset($dsn)) {
            $this->parse($dsn);
        }

        return $this;
    }

    /**
     * パース結果を返します。
     *
     * @return array of DSN attributes
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * DSN文字列を解析します。
     *
     * driver://username:password@hostname:port/database?option=value
     * driver://username:password@hostname:port/database
     * driver://username:password@hostname/database
     * driver://username@hostname:port/database
     * driver://username:password@hostname
     * driver://username@hostname/database
     * driver://username@hostname
     * driver://hostname:port/database
     * driver://hostname/database
     * driver://hostname:port
     * driver://hostname
     * driver:///database
     * sqlite:///path/to/file/
     * sqlite://C:\path\to\file
     * sqlite:/path/to/file
     * sqlite:C:\path\to\file
     * sqlite::memory:
     *
     * @param string $dsn DSN文字列
     * @return array
     */
    public function parse($dsn)
    {

        $this->attributes = [
            'driver' => null,
            'username' => null,
            'password' => null,
            'hostname' => null,
            'port' => null,
            'database' => null,
            'options' => null,
        ];

        $value = $this->parseDriver($dsn);

        if (!isset($this->attributes['driver'])) {
            throw new \InvalidArgumentException(
                sprintf('Invalid DSN string "%s" to parse.', $dsn)
            );
        }

        // SQLiteの場合はスキーム部のみ
        if ($this->attributes['driver'] === 'sqlite') {
            return $this->attributes;
        }

        if (strlen($value) >= 1) {
            $value = $this->parseUsernameAndPassword($value);
        }

        if (strlen($value) >= 1) {
            $value = $this->parseHostnameAndPort($value);
        }

        if (strlen($value) >= 1) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $value = $this->parseDatabaseAndOptions($value);
        }

        return $this->attributes;
    }

    /**
     * ドライバを取得し、取得した部分を除去した文字列を返します。
     *
     * @param string $value DSN文字列
     * @return string "driver://" までを除去した文字列
     */
    public function parseDriver($value)
    {
        $endOfScheme = strpos($value, '://');

        if ($endOfScheme === false) {

            if (strpos($value, 'sqlite:') === 0) {
                // sqlite:/path/to/file
                // sqlite:C:\path\to\file
                // sqlite::memory:
                $this->attributes['driver'] = 'sqlite';
                $database = rawurldecode(substr($value, 7));
                if (strlen($database) >= 1) {
                    $this->attributes['database'] = $database;
                }
            }

            return '';
        }

        $driver = substr($value, 0, $endOfScheme);

        if (strlen($driver) >= 1) {
            $this->attributes['driver'] = rawurldecode($driver);
        }

        $value = substr($value, $endOfScheme + 3);

        // sqlite:///path/to/file
        // sqlite://C:\path\to\file
        if ($this->attributes['driver'] === 'sqlite') {
            if (strlen($value) >= 1) {
                $this->attributes['database'] = rawurldecode($value);
            }
        }

        return $value;
    }

    /**
     * ユーザー名およびパスワードを取得し、取得した部分を除去した文字列を返します。
     *
     * @param string $value "driver://" までを除去した文字列
     * @return string "username:password@" までを除去した文字列
     */
    public function parseUsernameAndPassword($value)
    {
        $endOfUserAndPassword = strrpos($value, '@');

        if ($endOfUserAndPassword !== false) {
            $username = substr($value, 0, $endOfUserAndPassword);
            if (strlen($username) >= 1) {
                $usernameAndPassword = explode(':', $username);
                if (isset($usernameAndPassword[0]) && strlen($usernameAndPassword[0]) >= 1) {
                    $this->attributes['username'] = rawurldecode($usernameAndPassword[0]);
                }
                if (isset($usernameAndPassword[1]) && strlen($usernameAndPassword[1]) >= 1) {
                    $this->attributes['password'] = rawurldecode($usernameAndPassword[1]);
                }
            }
            $value = substr($value, $endOfUserAndPassword + 1);
        }

        return $value;
    }

    /**
     * ホスト名およびポートを取得し、取得した部分を除去した文字列を返します。
     *
     * @param string $value "username:password@" までを除去した文字列
     * @return string "hostname:port/" までを除去した文字列
     */
    public function parseHostnameAndPort($value)
    {

        // hostname(+port) と database(+option) を分割
        $hostspecAndDatabase = explode('/', $value, 2);

        if (count($hostspecAndDatabase) === 1) {
            $hostspec = $hostspecAndDatabase[0];
            $value = '';
        } else {
            $hostspec = $hostspecAndDatabase[0];
            $value = $hostspecAndDatabase[1];
        }

        if (strlen($hostspec) >= 1) {
            $hostnameAndPort = explode(':', $hostspec);
            if (isset($hostnameAndPort[0]) && strlen($hostnameAndPort[0]) >= 1) {
                $this->attributes['hostname'] = rawurldecode($hostnameAndPort[0]);
            }
            if (isset($hostnameAndPort[1]) && strlen($hostnameAndPort[1]) >= 1) {
                $this->attributes['port'] = rawurldecode($hostnameAndPort[1]);
            }
        }

        return $value;
    }

    /**
     * データベース名およびオプション引数を取得し、取得した部分を除去した文字列を返します。
     *
     * @param string $value "hostname:port/" までを除去した文字列
     * @return string "database?option=value" までを除去した文字列
     */
    public function parseDatabaseAndOptions($value)
    {
        $endOfDatabase = strpos($value, '?');

        if ($endOfDatabase === false) {
            $this->attributes['database'] = $value;
        } else {
            $database = substr($value, 0, $endOfDatabase);
            if (strlen($database) >= 1) {
                $this->attributes['database'] = rawurldecode($database);
            }
            $value = substr($value, $endOfDatabase + 1);
            parse_str($value, $parameters);
            if (!empty($parameters)) {
                $this->attributes['options'] = [];
                foreach ($parameters as $name => $val) {
                    $this->attributes['options'][$name] = $val;
                }
            }
        }

        return $value;
    }

}
