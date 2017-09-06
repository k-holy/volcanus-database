<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

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
	 * @param array $properties プロパティの配列
	 */
	public function __construct(array $properties = null)
	{
		if ($properties !== null) {
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
	}

	/**
	 * __isset
	 *
	 * @param mixed $name
	 * @return bool
	 */
	public function __isset($name)
	{
		return (property_exists($this, $name) && $this->{$name} !== null);
	}

	/**
	 * __get
	 *
	 * @param mixed $name
	 * @throws \InvalidArgumentException
	 */
	public function __get($name)
	{
		if (method_exists($this, 'get' . ucfirst($name))) {
			return $this->{'get' . ucfirst($name)}();
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
	 * @param mixed $name
	 * @param mixed $value
	 * @throws \InvalidArgumentException
	 */
	public function __set($name, $value)
	{
		if (method_exists($this, 'set' . ucfirst($name))) {
			$this->{'set' . ucfirst($name)}($value);
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
	 * @param mixed $name
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

	/**
	 * 現在日時をセットします。
	 *
	 * @param \DateTime $now
	 */
	private function setNow(\DateTime $now)
	{
		$this->now = $now;
	}

	/**
	 * 年齢を返します。
	 *
	 * @return int
	 */
	public function getAge()
	{
		if ($this->birthday !== null && $this->now !== null) {
			$birthday = \DateTime::createFromFormat('Y-m-d', $this->birthday);
			return (int)(((int)$this->now->format('Ymd') - (int)$birthday->format('Ymd')) / 10000);
		}
		return null;
	}

}
