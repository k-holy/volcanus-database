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
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($name, $value)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $name)
			);
		}
		return $this->{$name} = $value;
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($name)
	{
		if (!property_exists($this, $name)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $name)
			);
		}
		return $this->{$name};
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
				sprintf('The attribute "%s" does not exists.', $name)
			);
		}
		$this->{$name} = null;
	}

	/**
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($name)
	{
		return (property_exists($this, $name) && !is_null($this->{$name}));
	}

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
					'The properties is not Array and not Traversable.');
			}
			foreach ($properties as $name => $value) {
				$this->offsetSet($name, $value);
			}
			return $this;
		}
		throw new \InvalidArgumentException('Invalid argument count.');
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
	 * 配列に変換して返します。
	 *
	 * @return array
	 */
	public function toArray()
	{
		return $this->properties();
	}

	/**
	 * magic setter
	 *
	 * @param string プロパティ名
	 * @param mixed プロパティ値
	 */
	public function __set($name, $value)
	{
		$this->offsetSet($name, $value);
	}

	/**
	 * magic getter
	 *
	 * @param string プロパティ名
	 */
	public function __get($name)
	{
		return $this->offsetGet($name);
	}

	/**
	 * magic isset
	 *
	 * @param string プロパティ名
	 * @return bool
	 */
	public function __isset($name)
	{
		return $this->offsetExists($name);
	}

	/**
	 * magic unset
	 *
	 * @param string プロパティ名
	 */
	public function __unset($name)
	{
		$this->offsetUnset($name);
	}

	/**
	 * __toString
	 */
	public function __toString()
	{
		return var_export($this->properties(), true);
	}

	/**
	 * __sleep
	 *
	 * @param void
	 * @return void
	 */
	public function __sleep()
	{
		return array_keys($this->properties(), true);
	}

	/**
	 * __set_state
	 *
	 * @param array
	 * @return object
	 */
	public static function __set_state($attributes)
	{
		$instance = new static();
		$instance->properties($attributes);
		return $instance;
	}

}
