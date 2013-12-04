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
 * メタデータプロセッサ抽象クラス
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractMetaDataProcessor implements MetaDataProcessorInterface
{

	/**
	 * @var Volcanus\Database\MetaData\Cache\CacheProcessorInterface
	 */
	protected $cacheProcessor;

	/**
	 * キャッシュプロセッサをセットします。
	 *
	 * @param Volcanus\Database\MetaData\Cache\CacheProcessorInterface キャッシュプロセッサ
	 */
	public function setCacheProcessor(CacheProcessorInterface $cacheProcessor)
	{
		$this->cacheProcessor = $cacheProcessor;
	}

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @return array of Table
	 */
	public function getMetaTables(DriverInterface $driver)
	{
		if (isset($this->cacheProcessor)) {
			if ($this->cacheProcessor->hasMetaTables()) {
				return $this->cacheProcessor->getMetaTables();
			}
		}
		$tables = $this->doGetMetaTables($driver);
		if (isset($this->cacheProcessor)) {
			$this->cacheProcessor->setMetaTables($tables);
		}
		return $tables;
	}

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @param string テーブル名
	 * @return array of Column
	 */
	public function getMetaColumns(DriverInterface $driver, $table)
	{
		if (isset($this->cacheProcessor)) {
			if ($this->cacheProcessor->hasMetaColumns($table)) {
				return $this->cacheProcessor->getMetaColumns($table);
			}
		}
		$columns = $this->doGetMetaColumns($driver, $table);
		if (isset($this->cacheProcessor)) {
			$this->cacheProcessor->setMetaColumns($table, $columns);
		}
		return $columns;
	}

	/**
	 * テーブルオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @return array of Table
	 */
	abstract protected function doGetMetaTables(DriverInterface $driver);

	/**
	 * 指定テーブルのカラムオブジェクトを配列で返します。
	 *
	 * @param Volcanus\Database\Driver\DriverInterface データベースドライバ
	 * @param string テーブル名
	 * @return array of Column
	 */
	abstract protected function doGetMetaColumns(DriverInterface $driver, $table);

}
