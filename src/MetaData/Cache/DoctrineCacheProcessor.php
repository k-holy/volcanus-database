<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData\Cache;

use Doctrine\Common\Cache\Cache as DoctrineCacheInterface;

/**
 * Doctrine Cache プロセッサ
 *
 * @author k.holy74@gmail.com
 */
class DoctrineCacheProcessor implements CacheProcessorInterface
{

	const META_TABLES_ID = 'meta_tables';
	const META_COLUMNS_ID = 'meta_columns[%s]';

	/**
	 * @var object implements Doctrine\Common\Cache\Cache
	 */
	private $cache;

	/**
	 * コンストラクタ
	 *
	 * @param Doctrine\Common\Cache\Cache
	 */
	public function __construct(DoctrineCacheInterface $cache)
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
		return $this->cache->contains(
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
		return $this->cache->fetch(
			self::META_TABLES_ID
		);
	}

	/**
	 * テーブルオブジェクトの配列をキャッシュに保存します。
	 *
	 * @param array of Table
	 * @param int キャッシュの生存期間（秒） 0の場合は永続
	 * @return boolean 成功時はtrue、失敗時はfalse
	 */
	public function setMetaTables($tables, $lifetime = null)
	{
		if ($lifetime === null) {
			return $this->cache->save(
				self::META_TABLES_ID, $tables
			);
		}
		return $this->cache->save(
			self::META_TABLES_ID, $tables, $lifetime
		);
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
	 * @return boolean
	 */
	public function hasMetaColumns($table)
	{
		return $this->cache->contains(
			sprintf(self::META_COLUMNS_ID, $table)
		);
	}

	/**
	 * キャッシュから読み込んだ指定したテーブルのカラムオブジェクトの配列を返します。
	 *
	 * @param string テーブル名
	 * @return mixed null | array of Column from cache
	 */
	public function getMetaColumns($table)
	{
		return $this->cache->fetch(
			sprintf(self::META_COLUMNS_ID, $table)
		);
	}

	/**
	 * 指定したテーブルのカラムオブジェクトの配列をキャッシュに保存します。
	 *
	 * @param string テーブル名
	 * @param array of Column
	 * @param int キャッシュの生存期間（秒） 0の場合は永続
	 * @return boolean 成功時はtrue、失敗時はfalse
	 */
	public function setMetaColumns($table, $columns, $lifetime = null)
	{
		if ($lifetime === null) {
			return $this->cache->save(
				sprintf(self::META_COLUMNS_ID, $table), $columns
			);
		}
		return $this->cache->save(
			sprintf(self::META_COLUMNS_ID, $table), $columns, $lifetime
		);
	}

	/**
	 * 指定したテーブルのカラムオブジェクトの配列をキャッシュから破棄します。
	 *
	 * @param string テーブル名
	 * @return boolean 成功時はtrue、失敗時はfalse
	 */
	public function unsetMetaColumns($table)
	{
		return $this->cache->delete(
			sprintf(self::META_COLUMNS_ID, $table)
		);
	}

}
