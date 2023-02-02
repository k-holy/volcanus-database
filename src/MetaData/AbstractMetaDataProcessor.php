<?php
/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;

/**
 * メタデータプロセッサ抽象クラス
 *
 * @author k.holy74@gmail.com
 */
abstract class AbstractMetaDataProcessor implements MetaDataProcessorInterface
{

    /**
     * @var CacheProcessorInterface|null
     */
    protected ?CacheProcessorInterface $cacheProcessor = null;

    /**
     * キャッシュプロセッサをセットします。
     *
     * @param CacheProcessorInterface $cacheProcessor キャッシュプロセッサ
     */
    public function setCacheProcessor(CacheProcessorInterface $cacheProcessor)
    {
        $this->cacheProcessor = $cacheProcessor;
    }

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @param DriverInterface $driver データベースドライバ
     * @return Table[]
     */
    public function getMetaTables(DriverInterface $driver): array
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
     * @param DriverInterface $driver データベースドライバ
     * @param string $table テーブル名
     * @return Column[]
     */
    public function getMetaColumns(DriverInterface $driver, string $table): array
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
     * @param DriverInterface $driver データベースドライバ
     * @return Table[]
     */
    abstract protected function doGetMetaTables(DriverInterface $driver): array;

    /**
     * 指定テーブルのカラムオブジェクトを配列で返します。
     *
     * @param DriverInterface $driver データベースドライバ
     * @param string $table テーブル名
     * @return Column[]
     */
    abstract protected function doGetMetaColumns(DriverInterface $driver, string $table): array;

}
