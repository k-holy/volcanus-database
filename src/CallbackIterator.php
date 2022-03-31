<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
    private $callback;

    /**
     * コンストラクタ
     *
     * @param \Traversable $iterator
     * @param callable $callback 要素を返す際に実行するコールバック関数
     */
    public function __construct(\Traversable $iterator, callable $callback)
    {
        $this->callback = $callback;
        parent::__construct($iterator);
    }

    /**
     * Iterator::current
     *
     * @return mixed
     */
    public function current()
    {
        return call_user_func($this->callback, parent::current());
    }

}
