<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * カラムクラス
 *
 * @author k.holy74@gmail.com
 */
class Column implements \ArrayAccess, \IteratorAggregate
{

	/**
	 * @var string カラム名
	 */
	private $name;

	/**
	 * @var string データ型
	 */
	private $type;

	/**
	 * @var int 最大文字数
	 */
	private $maxLength;

	/**
	 * @var int 桁数
	 */
	private $scale;

	/**
	 * @var bool バイナリデータかどうか
	 */
	private $binary;

	/**
	 * @var mixed デフォルト値
	 */
	private $default;

	/**
	 * @var bool NOT NULL制約が付与されているかどうか
	 */
	private $notNull;

	/**
	 * @var bool PRIMARY KEYかどうか
	 */
	private $primaryKey;

	/**
	 * @var bool UNIQUE KEYかどうか
	 */
	private $uniqueKey;

	/**
	 * @var bool AUTO INCREMENTかどうか
	 */
	private $autoIncrement;

	/**
	 * @var string コメント
	 */
	private $comment;

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
