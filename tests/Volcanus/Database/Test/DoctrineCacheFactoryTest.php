<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test;

use Volcanus\Database\DoctrineCacheFactory;

/**
 * Test for DoctrineCacheFactory
 *
 * @author k.holy74@gmail.com
 */
class DoctrineCacheFactoryTest extends \PHPUnit\Framework\TestCase
{

    public function testCreateInstance()
    {
        $cache = DoctrineCacheFactory::create('array');
        $this->assertInstanceOf('\Doctrine\Common\Cache\ArrayCache', $cache);
    }

    public function testCreateInstanceWithParameters()
    {
        $cache = DoctrineCacheFactory::create('filesystem', [
            'directory' => __DIR__,
            'extension' => '.tmp',
        ]);
        $this->assertInstanceOf('\Doctrine\Common\Cache\FilesystemCache', $cache);
    }

    public function testCreateInstanceWithNamespace()
    {
        $cache = DoctrineCacheFactory::create('array', [
            'namespace' => 'Test',
        ]);
        $this->assertEquals('Test', $cache->getNamespace());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRaiseExceptionWhenUnsupportedProviderName()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $cache = DoctrineCacheFactory::create('unsupportedProvicerName');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testRaiseExceptionWhenUnsupportedParameter()
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $cache = DoctrineCacheFactory::create('array', [
            'unsupported_parameter' => 'foo',
        ]);
    }

    public function testExceptionMessageContainsUnsupportedParameterName()
    {
        try {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $cache = DoctrineCacheFactory::create('array', [
                'unsupportedParameter1' => 'foo',
                'unsupportedParameter2' => 'bar',
                'unsupportedParameter3' => 'baz',
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->assertContains('unsupportedParameter1', $e->getMessage());
            $this->assertContains('unsupportedParameter2', $e->getMessage());
            $this->assertContains('unsupportedParameter3', $e->getMessage());
        }
    }

}
