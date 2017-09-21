<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * Factory for Doctrine Cache Providers
 *
 * @author k.holy74@gmail.com
 */
class DoctrineCacheFactory
{

    /**
     * 指定された種別の Doctrine\Common\Cache\Cache オブジェクトを生成して返します。
     *
     * @param string $type キャッシュプロバイダ種別 [apc|array|couchbase|filesystem|memcache|memcached|mongoDB|phpFile|redis|riak|winCache|xcache|zendData]
     * @param array $options キャッシュプロバイダのコンストラクタ引数のオプション配列
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public static function create($type, array $options = array())
    {
        $class = sprintf('\\Doctrine\\Common\\Cache\\%sCache', ucfirst($type));

        if (!class_exists($class, true)) {
            throw new \InvalidArgumentException(
                sprintf('The Doctrine\'s cache provider "%s" is not found.', $type)
            );
        }

        if (count($options) === 0) {
            return new $class();
        }

        $refClass = new \ReflectionClass($class);

        $constructor = $refClass->getConstructor();

        $arguments = array();

        if ($constructor instanceof \ReflectionMethod) {
            foreach ($constructor->getParameters() as $param) {
                $paramName = $param->getName();
                if (array_key_exists($paramName, $options)) {
                    $arguments[] = $options[$paramName];
                    unset($options[$paramName]);
                } else {
                    $arguments[] = $param->getDefaultValue();
                }
            }
        }

        /** @var $provider \Doctrine\Common\Cache\CacheProvider */
        $provider = (count($arguments) >= 1)
            ? $refClass->newInstanceArgs($arguments)
            : $refClass->newInstance();

        if (isset($options['namespace']) && method_exists($provider, 'setNamespace')) {
            $provider->setNamespace($options['namespace']);
            unset($options['namespace']);
        }

        if (count($options) !== 0) {
            throw new \InvalidArgumentException(
                sprintf('Not supported parameter [%s]', implode(',', array_keys($options)))
            );
        }

        return $provider;
    }

}
