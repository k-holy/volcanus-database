<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\CallbackIterator;

/**
 * Test for CallbackIterator
 *
 * @author k.holy74@gmail.com
 */
class CallbackIteratorTest extends \PHPUnit\Framework\TestCase
{

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorRaiseInvalidArgumentExceptionWhenInvalidType()
	{
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection PhpParamsInspection */
		$iterator = new CallbackIterator(new \ArrayIterator(array()), false);
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testConstructorRaiseInvalidArgumentExceptionWhenInvalidObject()
	{
        /** @noinspection PhpUnusedLocalVariableInspection */
        /** @noinspection PhpParamsInspection */
		$iterator = new CallbackIterator(new \ArrayIterator(array()), new \StdClass());
	}

	public function testCurrent()
	{
		$values = array();
		$values[] = array('num' => 0);
		$values[] = array('num' => 1);
		$values[] = array('num' => 2);
		$iterator = new CallbackIterator(new \ArrayIterator($values), function($value) {
			$object = new \StdClass();
			$object->num = $value['num'];
			$object->pow = pow($value['num'], 2);
			return $object;
		});
		foreach ($iterator as $current) {
			$this->assertEquals(pow($current->num, 2), $current->pow);
		}
	}

}
