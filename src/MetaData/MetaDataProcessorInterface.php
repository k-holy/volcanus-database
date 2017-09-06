<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Volcanus\Database\Driver\DriverInterface;

/**
 * メタデータプロセッサインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface MetaDataProcessorInterface
{

	/**
	 * キャッシュプロセッサをセットします。
	 *
	 * @param \Volcanus\Database\MetaData\Cache\CacheProcessorInterface $cacheProcessor キャッシュプロセッサ
	 */
	public function setCacheProcessor(CacheProcessorInterface $cacheProcessor);

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface $driver データベースドライバ
	 * @return array of Table
	 */
	public function getMetaTables(DriverInterface $driver);

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface $driver データベースドライバ
	 * @param string $table テーブル名
	 * @return array of Column
	 */
	public function getMetaColumns(DriverInterface $driver, $table);

}
