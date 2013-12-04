<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests;

use Volcanus\Database\AbstractPropertyAccessor;

/**
 * Test for AbstractPropertyAccessor
 *
 * @author k.holy74@gmail.com
 */
class AbstractPropertyAccessorTest extends \PHPUnit_Framework_TestCase
{

	public function testOffsetSet()
	{
		$test = new Test();
		$test->offsetSet('string', 'Foo');
		$this->assertEquals('Foo', $test->string);
	}

	public function testOffsetGet()
	{
		$test = new Test();
		$test->string = 'Foo';
		$this->assertEquals('Foo', $test->offsetGet('string'));
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenSetUndefinedProperty()
	{
		$test = new Test();
		$test->offsetSet('not_defined_attribute', 'Foo');
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testRaiseExceptionWhenGetUndefinedProperty()
	{
		$test = new Test();
		$test->offsetGet('not_defined_attribute');
	}

	public function testOffsetUnset()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->offsetUnset('string');
		$this->assertNull($test->string);
		$this->assertEmpty($test->string);
	}

	public function testOffsetExists()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$this->assertTrue($test->offsetExists('string'));
		$this->assertFalse($test->offsetExists('null'));
		$this->assertFalse($test->offsetExists('not_defined_attribute'));
	}

	public function testArrayAccess()
	{
		$test = new Test();
		$test['string' ] = 'Foo';
		$test['null'   ] = null;
		$test['boolean'] = true;
		$this->assertEquals('Foo', $test['string']);
		$this->assertNull($test['null']);
		$this->assertTrue($test['boolean']);
	}

	public function testIssetArrayAccess()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$this->assertTrue(isset($test['string']));
		$this->assertFalse(isset($test['null']));
		$this->assertFalse(isset($test['not_defined_attribute']));
	}

	public function testIssetPropertyAccess()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$this->assertTrue(isset($test->string));
		$this->assertFalse(isset($test->null));
		$this->assertFalse(isset($test->not_defined_attribute));
	}

	public function testUnsetArrayAccess()
	{
		$test = new Test();
		$test->boolean = false;
		$this->assertTrue(isset($test['boolean']));
		unset($test['boolean']);
		$this->assertFalse(isset($test['boolean']));
	}

	public function testUnsetPropertyAccess()
	{
		$test = new Test();
		$test->boolean = false;
		$this->assertTrue(isset($test->boolean));
		unset($test->boolean);
		$this->assertFalse(isset($test->boolean));
	}

	public function testIsNullArrayAccess()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$this->assertFalse(is_null($test['string']));
		$this->assertTrue(is_null($test['null']));
	}

	public function testIsNullPropertyAccess()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$this->assertFalse(is_null($test->string));
		$this->assertTrue(is_null($test->null));
	}

	public function testTraversable()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$test->boolean = true;
		foreach ($test as $name => $value) {
			switch ($name) {
			case 'string':
				$this->assertEquals('Foo', $value);
				break;
			case 'null':
				$this->assertNull($value);
				break;
			case 'boolean':
				$this->assertTrue($value);
				break;
			}
		}
	}

	public function testSerialize()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$test->boolean = true;
		$this->assertEquals($test, unserialize(serialize($test)));
	}

	public function testVarExport()
	{
		$test = new Test();
		$test->string = 'Foo';
		$test->null = null;
		$test->boolean = true;
		eval('$exported = ' . var_export($test, true) . ';');
		$this->assertEquals($test, $exported);
	}

}

class Test extends AbstractPropertyAccessor
{
	protected $string;
	protected $null;
	protected $boolean;
}
