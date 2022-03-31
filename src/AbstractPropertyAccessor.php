<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
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
     * AbstractPropertyAccessor constructor.
     *
     * @param array $properties プロパティの配列
     */
    public function __construct(array $properties = [])
    {
        $this->initialize($properties);
    }

    /**
     * プロパティを引数の配列からセットして自身を返します。
     *
     * @param array $properties プロパティの配列
     * @return $this
     */
    public function initialize(array $properties = []): AbstractPropertyAccessor
    {
        foreach (array_keys(get_object_vars($this)) as $name) {
            $this->{$name} = null;
            if (array_key_exists($name, $properties)) {
                $this->{$name} = (is_object($properties[$name]))
                    ? clone $properties[$name]
                    : $properties[$name];
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
        return $this;
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
     */
    public function __get($name)
    {
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
     */
    public function __set($name, $value)
    {
        throw new \LogicException(
            sprintf('The property "%s" could not set.', $name)
        );
    }

    /**
     * __unset
     *
     * @param mixed $name
     */
    public function __unset($name)
    {
        throw new \LogicException(
            sprintf('The property "%s" could not unset.', $name)
        );
    }

    /**
     * __clone for clone
     */
    public function __clone()
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (is_object($value)) {
                $this->{$name} = clone $value;
            }
        }
    }

    /**
     * __sleep for serialize()
     *
     * @return array
     */
    public function __sleep()
    {
        return array_keys(get_object_vars($this));
    }

    /**
     * __set_state for var_export()
     *
     * @param array $properties
     * @return static
     */
    public static function __set_state(array $properties)
    {
        return new static($properties);
    }

    /**
     * IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator(get_object_vars($this));
    }

    /**
     * 配列に変換して返します。
     *
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * ArrayAccess::offsetExists()
     *
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name): bool
    {
        return $this->__isset($name);
    }

    /**
     * ArrayAccess::offsetGet()
     *
     * @param mixed $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    /**
     * ArrayAccess::offsetSet()
     *
     * @param mixed $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    /**
     * ArrayAccess::offsetUnset()
     *
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        $this->__unset($name);
    }

}
