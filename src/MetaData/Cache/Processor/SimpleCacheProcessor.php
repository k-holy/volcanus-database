<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData\Cache\Processor;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * PSR-16 Simple Cache プロセッサ
 *
 * @author k.holy74@gmail.com
 */
class SimpleCacheProcessor implements CacheProcessorInterface
{

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * コンストラクタ
     *
     * @param CacheInterface $cache
     */
    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * テーブルオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @return boolean
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function hasMetaTables()
    {
        return $this->cache->has(
            self::META_TABLES_ID
        );
    }

    /**
     * キャッシュから読み込んだテーブルオブジェクトの配列を返します。
     *
     * @return mixed null | array of Table from cache
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMetaTables()
    {
        return $this->cache->get(
            self::META_TABLES_ID
        );
    }

    /**
     * テーブルオブジェクトの配列をキャッシュに保存します。
     *
     * @param array $tables Tables
     * @param int $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return boolean 成功時はtrue、失敗時はfalse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMetaTables($tables, $lifetime = null)
    {
        if ($lifetime === null) {
            return $this->cache->set(
                self::META_TABLES_ID, $tables
            );
        }
        return $this->cache->set(
            self::META_TABLES_ID, $tables, $lifetime
        );
    }

    /**
     * テーブルオブジェクトの配列をキャッシュから破棄します。
     *
     * @return boolean 成功時はtrue、失敗時はfalse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function unsetMetaTables()
    {
        return $this->cache->delete(
            self::META_TABLES_ID
        );
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @param string $table テーブル名
     * @return boolean
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function hasMetaColumns($table)
    {
        return $this->cache->has(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

    /**
     * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
     *
     * @param string $table テーブル名
     * @return mixed null | array of Column from cache
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMetaColumns($table)
    {
        return $this->cache->get(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュに保存します。
     *
     * @param string $table テーブル名
     * @param array $columns Column
     * @param int $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return boolean 成功時はtrue、失敗時はfalse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setMetaColumns($table, $columns, $lifetime = null)
    {
        if ($lifetime === null) {
            return $this->cache->set(
                sprintf(self::META_COLUMNS_ID, $table), $columns
            );
        }
        return $this->cache->set(
            sprintf(self::META_COLUMNS_ID, $table), $columns, $lifetime
        );
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
     *
     * @param string $table テーブル名
     * @return boolean 成功時はtrue、失敗時はfalse
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function unsetMetaColumns($table)
    {
        return $this->cache->delete(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

}
