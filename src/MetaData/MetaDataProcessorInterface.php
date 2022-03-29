<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\MetaData\Column;
use Volcanus\Database\MetaData\Table;
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
     * @param CacheProcessorInterface $cacheProcessor キャッシュプロセッサ
     */
    public function setCacheProcessor(CacheProcessorInterface $cacheProcessor);

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @param DriverInterface $driver データベースドライバ
     * @return Table[]
     */
    public function getMetaTables(DriverInterface $driver): array;

    /**
     * 指定テーブルのカラムオブジェクトを配列で返します。
     *
     * @param DriverInterface $driver データベースドライバ
     * @param string $table テーブル名
     * @return Column[]
     */
    public function getMetaColumns(DriverInterface $driver, string $table): array;

}
