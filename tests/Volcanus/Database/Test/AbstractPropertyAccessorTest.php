<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\AbstractPropertyAccessor;

/**
 * Test for AbstractPropertyAccessor
 *
 * @author k.holy74@gmail.com
 */
class AbstractPropertyAccessorTest extends \PHPUnit_Framework_TestCase
{

    public function testConstructorDefensiveCopy()
    {
        $now = new \DateTime();
        $test = new Test(array(
            'datetime' => $now,
        ));
        $this->assertEquals($now, $test->datetime);
        $this->assertNotSame($now, $test->datetime);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorRaiseInvalidArgumentExceptionUndefinedProperty()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $test = new Test(array(
            'undefined_property' => 'Foo',
        ));
    }

    public function testIsset()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
        ));
        $this->assertTrue(isset($test->string));
        $this->assertFalse(isset($test->null));
        $this->assertFalse(isset($test->undefined_property));
    }

    public function testGet()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
        ));
        $this->assertEquals('Foo', $test->string);
        $this->assertNull($test->null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRaiseInvalidArgumentExceptionUndefinedProperty()
    {
        $test = new Test();
        /** @noinspection PhpUndefinedFieldInspection */
        $test->undefined_property;
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetRaiseLogicException()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'boolean' => true,
        ));
        $test->string = 'Bar';
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnsetRaiseLogicException()
    {
        $test = new Test(array(
            'string' => 'Foo',
        ));
        unset($test->string);
    }

    public function testSerializable()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ));
        $serialized = serialize($test);
        $this->assertEquals($test, unserialize($serialized));
        $this->assertNotSame($test, unserialize($serialized));
    }

    public function testVarExport()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ));
        eval('$exported = ' . var_export($test, true) . ';');
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertEquals($test, $exported);
        $this->assertNotSame($test, $exported);
    }

    public function testClone()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ));
        $cloned = clone $test;
        $this->assertEquals($test->datetime, $cloned->datetime);
        $this->assertNotSame($test->datetime, $cloned->datetime);
    }

    public function testTraversable()
    {
        $properties = array(
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        );
        $test = new Test($properties);
        foreach ($test as $name => $value) {
            if (array_key_exists($name, $properties)) {
                $this->assertEquals($properties[$name], $value);
                if (is_object($value)) {
                    $this->assertNotSame($properties[$name], $value);
                }
            }
        }
    }

    public function testToArray()
    {
        $properties = array(
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        );
        $test = new Test($properties);
        $this->assertEquals($properties, $test->toArray());
        $this->assertNotSame($properties, $test->toArray());
    }

    public function testIssetByArrayAccess()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
        ));
        $this->assertTrue(isset($test['string']));
        $this->assertFalse(isset($test['null']));
        $this->assertFalse(isset($test['not_defined_property']));
    }

    public function testGetByArrayAccess()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'null' => null,
        ));
        $this->assertEquals('Foo', $test['string']);
        $this->assertNull($test['null']);
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetRaiseLogicExceptionByArrayAccess()
    {
        $test = new Test(array(
            'string' => 'Foo',
            'boolean' => true,
        ));
        $test['string'] = 'Bar';
    }

    /**
     * @expectedException \LogicException
     */
    public function testUnsetRaiseLogicExceptionByArrayAccess()
    {
        $test = new Test(array(
            'string' => 'Foo',
        ));
        unset($test['string']);
    }

}

/**
 * Class Test
 *
 * @property string $string
 * @property $null
 * @property boolean $boolean
 * @property \DateTime $datetime
 */
class Test extends AbstractPropertyAccessor
{
    protected $string;
    protected $null;
    protected $boolean;
    protected $datetime;

    public function __construct(array $properties = array())
    {
        $this->initialize($properties);
    }

}
