<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
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

    /**
     * テーブルオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @return boolean
     */
    public function hasMetaTables();

    /**
     * キャッシュから読み込んだテーブルオブジェクトの配列を返します。
     *
     * @return mixed null | array of Table from cache
     */
    public function getMetaTables();

    /**
     * テーブルオブジェクトの配列をキャッシュに保存します。
     *
     * @param array of Table
     * @param int キャッシュの生存期間（秒） 0の場合は永続
     * @return boolean 成功時はtrue、失敗時はfalse
     */
    public function setMetaTables($tables, $lifetime = null);

    /**
     * テーブルオブジェクトの配列をキャッシュから破棄します。
     *
     * @return boolean 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaTables();

    /**
     * 指定したテーブルのカラムオブジェクトの配列がキャッシュに存在するかどうかを返します。
     *
     * @return boolean
     */
    public function hasMetaColumns($table);

    /**
     * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
     *
     * @param string テーブル名
     * @return mixed null | array of Column from cache
     */
    public function getMetaColumns($table);

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュに保存します。
     *
     * @param string テーブル名
     * @param array of Column
     * @param int キャッシュの生存期間（秒） 0の場合は永続
     * @return boolean 成功時はtrue、失敗時はfalse
     */
    public function setMetaColumns($table, $columns, $lifetime = null);

    /**
     * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
     *
     * @param string テーブル名
     * @return boolean 成功時はtrue、失敗時はfalse
     */
    public function unsetMetaColumns($table);

}
