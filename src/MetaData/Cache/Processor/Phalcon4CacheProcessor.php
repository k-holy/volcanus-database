<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData\Cache\Processor;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Phalcon\Cache\Adapter\AdapterInterface;

/**
 * Phalcon4 Cache プロセッサ
 *
 * @author k.holy74@gmail.com
 */
class Phalcon4CacheProcessor implements CacheProcessorInterface
{

    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * コンストラクタ
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * テーブルオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @return bool
     */
    public function hasMetaTables(): bool
    {
        return $this->adapter->has(
            self::META_TABLES_ID
        );
    }

    /**
     * キャッシュから読み込んだテーブルオブジェクトの配列を返します。
     *
     * @return mixed null | array of Table from cache
     */
    public function getMetaTables()
    {
        return $this->adapter->get(
            self::META_TABLES_ID
        );
    }

    /**
     * テーブルオブジェクトの配列をキャッシュに保存します。
     *
     * @param array $tables Tables
     * @param int|null $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function setMetaTables(array $tables, int $lifetime = null): bool
    {
        if ($lifetime === null) {
            $this->adapter->set(
                self::META_TABLES_ID, $tables
            );
        } else {
            $this->adapter->set(
                self::META_TABLES_ID, $tables, $lifetime
            );
        }
        return true;
    }

    /**
     * テーブルオブジェクトの配列をキャッシュから破棄します。
     *
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaTables(): bool
    {
        return $this->adapter->delete(
            self::META_TABLES_ID
        );
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @param string $table テーブル名
     * @return bool
     */
    public function hasMetaColumns(string $table): bool
    {
        return $this->adapter->has(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

    /**
     * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
     *
     * @param string $table テーブル名
     * @return mixed null | array of Column from cache
     */
    public function getMetaColumns(string $table)
    {
        return $this->adapter->get(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュに保存します。
     *
     * @param string $table テーブル名
     * @param array $columns Column
     * @param int|null $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function setMetaColumns(string $table, array $columns, int $lifetime = null): bool
    {
        if ($lifetime === null) {
            $this->adapter->set(
                sprintf(self::META_COLUMNS_ID, $table), $columns
            );
        } else {
            $this->adapter->set(
                sprintf(self::META_COLUMNS_ID, $table), $columns, $lifetime
            );
        }
        return true;
    }

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
     *
     * @param string $table テーブル名
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaColumns(string $table): bool
    {
        return $this->adapter->delete(
            sprintf(self::META_COLUMNS_ID, $table)
        );
    }

}
