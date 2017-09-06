<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Statement;

/**
 * MySQL メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class MysqlMetaDataProcessor extends AbstractMetaDataProcessor
{

    /**
     * コンストラクタ
     *
     * @param \Volcanus\Database\MetaData\Cache\CacheProcessorInterface $cacheProcessor キャッシュプロセッサ
     */
    public function __construct(CacheProcessorInterface $cacheProcessor = null)
    {
        if ($cacheProcessor !== null) {
            $this->setCacheProcessor($cacheProcessor);
        }
    }

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @param \Volcanus\Database\Driver\DriverInterface $driver データベースドライバ
     * @return array of Table
     */
    protected function doGetMetaTables(DriverInterface $driver)
    {
        $tableListStatement = $driver->query($this->tableList());
        $tableListStatement->setFetchMode(Statement::FETCH_NUM);
        $tables = [];
        foreach ($tableListStatement->getIterator() as $cols) {
            $tables[$cols[0]] = new Table([
                'name' => $cols[0],
            ]);
        }
        return $tables;
    }

    /**
     * 指定テーブルのカラムオブジェクトを配列で返します。
     *
     * @param \Volcanus\Database\Driver\DriverInterface $driver データベースドライバ
     * @param string $table テーブル名
     * @return array of Column
     */
    protected function doGetMetaColumns(DriverInterface $driver, $table)
    {
        $columnsStatement = $driver->query($this->showFullColumnsFrom($table));
        $columnsStatement->setFetchMode(Statement::FETCH_ASSOC);
        $columns = [];
        foreach ($columnsStatement->getIterator() as $cols) {
            $name = $cols['Field'];
            $type = $cols['Type'];
            $maxLength = null;
            $scale = null;
            if (preg_match('/^(.+)\((\d+),(\d+)/', $type, $matches)) {
                $type = $matches[1];
                $maxLength = ctype_digit($matches[2]) ? $matches[2] : -1;
                $scale = ctype_digit($matches[3]) ? $matches[3] : -1;
            } elseif (preg_match('/^(.+)\((\d+)/', $type, $matches)) {
                $type = $matches[1];
                $maxLength = ctype_digit($matches[2]) ? $matches[2] : -1;
            } elseif (preg_match('/^(enum)\((.*)\)$/i', $type, $matches)) {
                $type = $matches[1];
                $zlen = max(array_map('strlen', explode(',', $matches[2]))) - 2;
                $maxLength = ($zlen > 0) ? $zlen : 1;
            }
            $notNull = ($cols['Null'] !== 'YES');
            $primaryKey = ($cols['Key'] === 'PRI');
            $uniqueKey = ($cols['Key'] === 'UNI');
            $autoIncrement = (strpos($cols['Extra'], 'auto_increment') !== false);
            $binary = (strpos($type, 'blob') !== false);
            $default = null;
            if (!$binary && isset($cols['Default']) && strcasecmp($cols['Default'], 'NULL') !== 0) {
                $default = $cols['Default'];
            }
            $comment = (isset($cols['Comment']) && strcmp($cols['Comment'], '') != 0) ? $cols['Comment'] : null;
            $columns[$name] = new Column([
                'name' => $name,
                'type' => $type,
                'maxLength' => $maxLength,
                'scale' => $scale,
                'binary' => $binary,
                'default' => $default,
                'notNull' => $notNull,
                'primaryKey' => $primaryKey,
                'uniqueKey' => $uniqueKey,
                'autoIncrement' => $autoIncrement,
                'comment' => $comment,
            ]);
        }

        return $columns;
    }

    /**
     * テーブル情報を取得するクエリを返します。
     *
     * @return string SQL
     */
    private function tableList()
    {
        return 'SHOW TABLES;';
    }

    /**
     * 指定テーブルのカラム情報を取得するクエリを返します。
     *
     * @param string $table テーブル名
     * @return string SQL
     */
    private function showFullColumnsFrom($table)
    {
        return sprintf('SHOW FULL COLUMNS FROM %s', $table);
    }

}
