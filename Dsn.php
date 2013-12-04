<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * DSN
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
	 * @param mixed string | array | Traversable
	 */
	public function __construct($attributes = null)
	{
		if ($attributes !== null) {
			$this->properties($attributes);
		}
	}

	/**
	 * PDO用のDSNを返します。
	 *
	 * @return string PDO用DSN
	 */
	public function toPdo()
	{
		switch ($this->driver) {
		case 'sqlite':
			return sprintf('%s:%s', $this->driver, $this->database);
		case 'mysql':
			$parameters = array();
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
			}
			if (isset($this->options['charset'])) {
				$parameters[] = sprintf('charset=%s', $this->options['charset']);
			}
			return sprintf('%s:%s', $this->driver, implode(';', $parameters));
		}
		throw new \InvalidArgumentException(
			sprintf('The driver "%s" is not supported.', $this->driver)
		);
	}

}
