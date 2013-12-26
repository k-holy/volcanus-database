<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\Driver\Pdo;

/**
 * TestData for PdoStatement
 *
 * @author k.holy74@gmail.com
 */
class PdoStatementTestData
{
	/**
	 * @var string
	 */
	private $userId;

	/**
	 * @var string
	 */
	private $userName;

	/**
	 * @var string Y-m-d
	 */
	private $birthday;

	/**
	 * @var string タイムスタンプ値
	 */
	private $createdAt;

	/**
	 * @var \DateTime 現在日時
	 */
	private $now;

	/**
	 * __construct()
	 *
	 * @param array プロパティの配列
	 */
	public function __construct(array $properties = null)
	{
		foreach (array_keys(get_object_vars($this)) as $name) {
			$this->{$name} = null;
			if (array_key_exists($name, $properties)) {
				$this->__set($name, $properties[$name]);
				unset($properties[$name]);
			}
		}
		if (count($properties) !== 0) {
			throw new \InvalidArgumentException(
				sprintf('Not supported properties [%s]',
					implode(',', array_keys($properties))
				)
			);
		}
	}

	/**
	 * __isset
	 *
	 * @param mixed
	 * @return bool
	 */
	public function __isset($name)
	{
		return (property_exists($this, $name) && $this->{$name} !== null);
	}

	/**
	 * __get
	 *
	 * @param mixed
	 * @throws \InvalidArgumentException
	 */
	public function __get($name)
	{
		$camelize = $this->camelize($name);
		if (method_exists($this, 'get' . $camelize)) {
			return $this->{'get' . $camelize}();
		}
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		return $this->{$name};
	}

	/**
	 * __set
	 *
	 * @param mixed
	 * @param mixed
	 * @throws \InvalidArgumentException
	 */
	public function __set($name, $value)
	{
		$camelize = $this->camelize($name);
		if (method_exists($this, 'set' . $camelize)) {
			return $this->{'set' . $camelize}($value);
		}
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		$this->{$name} = $value;
	}

	/**
	 * __unset
	 *
	 * @param mixed
	 * @throws \InvalidArgumentException
	 */
	public function __unset($name)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		$this->{$name} = null;
	}

	public function getAge()
	{
		if (isset($this->birthday)) {
			$birthday = \DateTime::createFromFormat('Y-m-d', $this->birthday);
			return (int)(((int)$this->now->format('Ymd') - (int)$birthday->format('Ymd')) / 10000);
		}
		return null;
	}

	/**
	 * @param string  $string
	 * @return string
	 */
	private function camelize($string)
	{
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
	}

}