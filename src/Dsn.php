<?php /** @noinspection PhpUnused */

/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * @var string|null ドライバ種別 (mysql, pgsql, sqlite)
     */
    protected ?string $driver = null;

    /**
     * @var string|null データベース名
     */
    protected ?string $database = null;

    /**
     * @var string|null ホスト名
     */
    protected ?string $hostname = null;

    /**
     * @var string|null ポート番号
     */
    protected ?string $port = null;

    /**
     * @var string|null ユーザ名
     */
    protected ?string $username = null;

    /**
     * @var string|null パスワード
     */
    protected ?string $password = null;

    /**
     * @var array|null ドライバ固有の接続オプション
     */
    protected ?array $options = null;

    /**
     * コンストラクタ
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }

    /**
     * DSN文字列からインスタンスを生成します。
     *
     * @param string $dsn
     * @return self
     */
    public static function createFromString(string $dsn): Dsn
    {
        $parser = new DsnParser($dsn);
        return new self($parser->getAttributes());
    }

    /**
     * PDO用のDSNを返します。
     *
     * @return string PDO用DSN
     */
    public function toPdo(): string
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
