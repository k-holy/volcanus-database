<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData\Cache;

/**
 * Phalcon Cache プロセッサ
 *
 * @author k.holy74@gmail.com
 */
class PhalconCacheProcessor implements CacheProcessorInterface
{

	const META_TABLES_ID = 'meta_tables';
	const META_COLUMNS_ID = 'meta_columns[%s]';

	/**
	 * @var object implements Phalcon\Common\Cache\Cache
	 */
	private $cache;

	/**
	 * コンストラクタ
	 *
	 * @param \Phalcon\Cache\BackendInterface
	 */
	public function __construct(\Phalcon\Cache\BackendInterface $cache)
	{
		$this->cache = $cache;
	}

	/**
	 * テーブルオブジェクトの配列がキャッシュに存在するかどうかを返します。
	 *
	 * @return boolean
	 */
	public function hasMetaTables()
	{
		return $this->cache->exists(
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
	 */
	public function setMetaTables($tables, $lifetime = null)
	{
		if ($lifetime === null) {
			$this->cache->save(
				self::META_TABLES_ID, $tables
			);
		} else {
			$this->cache->save(
				self::META_TABLES_ID, $tables, $lifetime
			);
		}
		return true;
	}

	/**
	 * テーブルオブジェクトの配列をキャッシュから破棄します。
	 *
	 * @return boolean 成功時はtrue、失敗時はfalse
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
	 */
	public function hasMetaColumns($table)
	{
		return $this->cache->exists(
			sprintf(self::META_COLUMNS_ID, $table)
		);
	}

	/**
	 * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
	 *
     * @param string $table テーブル名
	 * @return mixed null | array of Column from cache
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
	 */
	public function setMetaColumns($table, $columns, $lifetime = null)
	{
		if ($lifetime === null) {
			$this->cache->save(
				sprintf(self::META_COLUMNS_ID, $table), $columns
			);
		} else {
			$this->cache->save(
				sprintf(self::META_COLUMNS_ID, $table), $columns, $lifetime
			);
		}
		return true;
	}

	/**
	 * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
	 *
     * @param string $table テーブル名
	 * @return boolean 成功時はtrue、失敗時はfalse
	 */
	public function unsetMetaColumns($table)
	{
		return $this->cache->delete(
			sprintf(self::META_COLUMNS_ID, $table)
		);
	}

}
