<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * プロパティアクセッサ抽象クラス
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractPropertyAccessor implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * 引数なしの場合は全てのプロパティを配列で返します。
	 * 引数ありの場合は全てのプロパティを引数の配列からセットして$thisを返します。
	 *
	 * @param array プロパティの配列
	 * @return mixed プロパティの配列 または $this
	 */
	public function properties()
	{
		switch (func_num_args()) {
		case 0:
			return get_object_vars($this);
		case 1:
			$properties = func_get_arg(0);
			if (!is_array($properties) && !($properties instanceof \Traversable)) {
				throw new \InvalidArgumentException(
					sprintf('The properties is not an Array and not Traversable. type:%s',
						is_object($properties)
							? get_class($properties)
							: gettype($properties)
					)
				);
			}
			foreach ($properties as $name => $value) {
				if (!property_exists($this, $name)) {
					throw new \InvalidArgumentException(
						sprintf('The property "%s" does not exists.', $name)
					);
				}
				$this->{$name} = $value;
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param string プロパティ名
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return (property_exists($this, $name) && $this->{$name} !== null);
	}

	/**
	 * __isset
	 *
	 * @param string プロパティ名
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param string プロパティ名
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		return $this->{$name};
	}

	/**
	 * __get
	 *
	 * @param string プロパティ名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($name, $value)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		return $this->{$name} = $value;
	}

	/**
	 * __set
	 *
	 * @param string プロパティ名
	 * @param mixed プロパティ値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($name)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The property "%s" does not exists.', $name)
			);
		}
		$this->{$name} = null;
	}

	/**
	 * __unset
	 *
	 * @param string プロパティ名
	 */
	public function __unset($name)
	{
		$this->offsetUnset($name);
	}

	/**
	 * __toString
	 *
	 * @return string
	 */
	public function __toString()
	{
		return var_export($this->properties(), true);
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->properties());
	}

	/**
	 * __sleep
	 *
	 * @return array
	 */
	public function __sleep()
	{
		return array_keys($this->properties());
	}

	/**
	 * __clone
	 */
	public function __clone()
	{
		foreach ($this->properties() as $name => $value) {
			if (is_object($value)) {
				$this->{$name} = clone $value;
			}
		}
	}

	/**
	 * __set_state
	 *
	 * @param array
	 * @return object
	 */
	public static function __set_state($properties)
	{
		$instance = new static();
		$instance->properties($properties);
		return $instance;
	}

	/**
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->properties();
	}

}
