<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * Callback イテレータ
 *
 * @author k.holy74@gmail.com
 */
class CallbackIterator extends \IteratorIterator
{

	/**
	 * @var callable 要素を返す際に実行するコールバック関数
	 */
	private $function;

	/**
	 * コンストラクタ
	 *
	 * @param \Traversable
	 * @param callable 要素を返す際に実行するコールバック関数
	 */
	public function __construct(\Traversable $iterator, $function)
	{
		if (!is_callable($function)) {
			throw new \InvalidArgumentException(
				sprintf('CallbackIterator accepts only callable, invalid type:%s',
					(is_object($function))
						? get_class($function)
						: gettype($function)
				)
			);
		}
		$this->function = $function;
		parent::__construct($iterator);
	}

	/**
	 * Iterator::current
	 *
	 * @return mixed
	 */
	public function current()
	{
		return call_user_func_array($this->function, parent::current());
	}

}
