<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
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

    public function testCurrent()
    {
        $values = [];
        $values[] = ['num' => 0];
        $values[] = ['num' => 1];
        $values[] = ['num' => 2];
        $iterator = new CallbackIterator(new \ArrayIterator($values), function ($value) {
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
