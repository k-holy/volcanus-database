<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData\Cache;

/**
 * キャッシュプロセッサインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface CacheProcessorInterface
{

    const META_TABLES_ID = 'meta_tables';
    const META_COLUMNS_ID = 'meta_columns[%s]';

    /**
     * テーブルオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @return bool
     */
    public function hasMetaTables(): bool;

    /**
     * キャッシュから読み込んだテーブルオブジェクトの配列を返します。
     *
     * @return mixed null | array of Table from cache
     */
    public function getMetaTables(): mixed;

    /**
     * テーブルオブジェクトの配列をキャッシュに保存します。
     *
     * @param array $tables Tables
     * @param int|null $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function setMetaTables(array $tables, int $lifetime = null): bool;

    /**
     * テーブルオブジェクトの配列をキャッシュから破棄します。
     *
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaTables(): bool;

    /**
     * 指定したテーブルのカラムオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @param string $table テーブル名
     * @return bool
     */
    public function hasMetaColumns(string $table): bool;

    /**
     * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
     *
     * @param string $table テーブル名
     * @return mixed null | array of Column from cache
     */
    public function getMetaColumns(string $table): mixed;

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュに保存します。
     *
     * @param string $table テーブル名
     * @param array $columns Column
     * @param int|null $lifetime キャッシュの生存期間（秒） 0の場合は永続
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function setMetaColumns(string $table, array $columns, int $lifetime = null): bool;

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
     *
     * @param string $table テーブル名
     * @return bool 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaColumns(string $table): bool;

}
