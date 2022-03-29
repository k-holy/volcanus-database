<?php /** @noinspection PhpMultipleClassesDeclarationsInOneFile */

/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\AbstractPropertyAccessor;

/**
 * Test for AbstractPropertyAccessor
 *
 * @author k.holy74@gmail.com
 */
class AbstractPropertyAccessorTest extends \PHPUnit\Framework\TestCase
{

    public function testConstructorDefensiveCopy()
    {
        $now = new \DateTime();
        $test = new Test([
            'datetime' => $now,
        ]);
        $this->assertEquals($now, $test->datetime);
        $this->assertNotSame($now, $test->datetime);
    }

    public function testConstructorRaiseInvalidArgumentExceptionUndefinedProperty()
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @noinspection PhpUnusedLocalVariableInspection */
        $test = new Test([
            'undefined_property' => 'Foo',
        ]);
    }

    public function testIsset()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
        ]);
        $this->assertTrue(isset($test->string));
        $this->assertFalse(isset($test->null));
        $this->assertFalse(isset($test->undefined_property));
    }

    public function testGet()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
        ]);
        $this->assertEquals('Foo', $test->string);
        $this->assertNull($test->null);
    }

    public function testGetRaiseInvalidArgumentExceptionUndefinedProperty()
    {
        $this->expectException(\InvalidArgumentException::class);
        $test = new Test();
        /** @noinspection PhpUndefinedFieldInspection */
        /** @noinspection PhpExpressionResultUnusedInspection */
        $test->undefined_property;
    }

    public function testSetRaiseLogicException()
    {
        $this->expectException(\LogicException::class);
        $test = new Test([
            'string' => 'Foo',
            'boolean' => true,
        ]);
        $test->string = 'Bar';
    }

    public function testUnsetRaiseLogicException()
    {
        $this->expectException(\LogicException::class);
        $test = new Test([
            'string' => 'Foo',
        ]);
        unset($test->string);
    }

    public function testSerializable()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ]);
        $serialized = serialize($test);
        $this->assertEquals($test, unserialize($serialized));
        $this->assertNotSame($test, unserialize($serialized));
    }

    public function testVarExport()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ]);
        eval('$exported = ' . var_export($test, true) . ';');
        /** @noinspection PhpUndefinedVariableInspection */
        $this->assertEquals($test, $exported);
        $this->assertNotSame($test, $exported);
    }

    public function testClone()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ]);
        $cloned = clone $test;
        $this->assertEquals($test->datetime, $cloned->datetime);
        $this->assertNotSame($test->datetime, $cloned->datetime);
    }

    public function testTraversable()
    {
        $properties = [
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ];
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
        $properties = [
            'string' => 'Foo',
            'null' => null,
            'boolean' => true,
            'datetime' => new \DateTime(),
        ];
        $test = new Test($properties);
        $this->assertEquals($properties, $test->toArray());
        $this->assertNotSame($properties, $test->toArray());
    }

    public function testIssetByArrayAccess()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
        ]);
        $this->assertTrue(isset($test['string']));
        $this->assertFalse(isset($test['null']));
        $this->assertFalse(isset($test['not_defined_property']));
    }

    public function testGetByArrayAccess()
    {
        $test = new Test([
            'string' => 'Foo',
            'null' => null,
        ]);
        $this->assertEquals('Foo', $test['string']);
        $this->assertNull($test['null']);
    }

    public function testSetRaiseLogicExceptionByArrayAccess()
    {
        $this->expectException(\LogicException::class);
        $test = new Test([
            'string' => 'Foo',
            'boolean' => true,
        ]);
        $test['string'] = 'Bar';
    }

    public function testUnsetRaiseLogicExceptionByArrayAccess()
    {
        $this->expectException(\LogicException::class);
        $test = new Test([
            'string' => 'Foo',
        ]);
        unset($test['string']);
    }

}

/**
 * Class Test
 *
 * @property string $string
 * @property $null
 * @property bool $boolean
 * @property \DateTime $datetime
 */
class Test extends AbstractPropertyAccessor
{
    protected $string;
    protected $null;
    protected $boolean;
    protected $datetime;

    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }

}
