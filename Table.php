<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * テーブルクラス
 *
 * @author k.holy74@gmail.com
 */
class Table implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * @var string テーブル名
	 */
	private $name;

	/**
	 * @var string コメント
	 */
	private $comment;

	/**
	 * @var array カラム配列
	 */
	private $columns;

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
	 * ArrayAccess::offsetExists()
	 *
	 * @param mixed
	 * @return bool
	 */
	public function offsetExists($offset)
	{
		return (property_exists($this, $offset) && isset($this->{$offset}));
	}

	/**
	 * ArrayAccess::offsetGet()
	 *
	 * @param mixed
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		if (!property_exists($this, $offset)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset)
			);
		}
		return $this->{$offset};
	}

	/**
	 * ArrayAccess::offsetSet()
	 *
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet($offset, $value)
	{
		if (!property_exists($this, $offset)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset)
			);
		}
		return $this->{$offset} = $value;
	}

	/**
	 * ArrayAccess::offsetUnset()
	 *
	 * @param mixed
	 */
	public function offsetUnset($offset)
	{
		if (!property_exists($this, $offset)) {
			throw new \InvalidArgumentException(
				sprintf('The attribute "%s" does not exists.', $offset)
			);
		}
		$this->{$offset} = null;
	}

	/**
	 * IteratorAggregate::getIterator()
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator()
	{
		return new \ArrayIterator(get_object_vars($this));
	}

}
