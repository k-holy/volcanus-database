<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

use Volcanus\Database\DsnParser;

/**
 * DSN
 *
 * @property string $driver
 * @property string $database
 * @property string $hostname
 * @property string $port
 * @property string $username
 * @property string $password
 * @property array $options
 *
 * @author k.holy74@gmail.com
 */
class Dsn extends AbstractPropertyAccessor
{

    /**
     * @var string ドライバ種別 (mysql, pgsql, sqlite)
     */
    protected $driver;

    /**
     * @var string データベース名
     */
    protected $database;

    /**
     * @var string ホスト名
     */
    protected $hostname;

    /**
     * @var string ポート番号
     */
    protected $port;

    /**
     * @var string ユーザ名
     */
    protected $username;

    /**
     * @var string パスワード
     */
    protected $password;

    /**
     * @var array ドライバ固有の接続オプション
     */
    protected $options;

    /**
     * コンストラクタ
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $this->initialize($properties);
    }

    /**
     * DSN文字列からインスタンスを生成します。
     *
     * @param string $dsn
     * @return self
     */
    public static function createFromString($dsn)
    {
        $parser = new DsnParser($dsn);
        return new self($parser->getAttributes());
    }

    /**
     * PDO用のDSNを返します。
     *
     * @return string PDO用DSN
     */
    public function toPdo()
    {
        $options = $this->options;

        switch ($this->driver) {
            case 'sqlite':
                $dsn = sprintf('%s:%s', $this->driver, $this->database);
                break;
            case 'mysql':
                $parameters = [];
                if (isset($this->hostname)) {
                    $parameters[] = sprintf('host=%s', $this->hostname);
                }
                if (isset($this->port)) {
                    $parameters[] = sprintf('port=%s', $this->port);
                }
                if (isset($this->database)) {
                    $parameters[] = sprintf('dbname=%s', $this->database);
                }
                if (isset($this->options['unix_socket'])) {
                    $parameters[] = sprintf('unix_socket=%s', $this->options['unix_socket']);
                    unset($options['unix_socket']);
                }
                if (isset($this->options['charset'])) {
                    $parameters[] = sprintf('charset=%s', $this->options['charset']);
                    unset($options['charset']);
                }
                $dsn = sprintf('%s:%s', $this->driver, implode(';', $parameters));
                break;
            case 'pgsql':
                $parameters = [];
                if (isset($this->hostname)) {
                    $parameters[] = sprintf('host=%s', $this->hostname);
                }
                if (isset($this->port)) {
                    $parameters[] = sprintf('port=%s', $this->port);
                }
                if (isset($this->database)) {
                    $parameters[] = sprintf('dbname=%s', $this->database);
                }
                if (isset($this->username)) {
                    $parameters[] = sprintf('user=%s', $this->username);
                }
                if (isset($this->password)) {
                    $parameters[] = sprintf('password=%s', $this->password);
                }
                $dsn = sprintf('%s:%s', $this->driver, implode(';', $parameters));
                break;
        }

        if (!empty($options)) {
            throw new \RuntimeException(
                sprintf('Not supported option [%s]', implode(',', array_keys($options)))
            );
        }

        if (!isset($dsn)) {
            throw new \RuntimeException(
                sprintf('The driver "%s" is not supported.', $this->driver)
            );
        }

        return $dsn;
    }

}
