<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\MetaData\Cache\CacheProcessorInterface;
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;
use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\Statement;

/**
 * SQLite メタデータプロセッサ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteMetaDataProcessor extends AbstractMetaDataProcessor
{

    /**
     * コンストラクタ
     *
     * @param CacheProcessorInterface|null $cacheProcessor キャッシュプロセッサ
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
     * @param DriverInterface $driver データベースドライバ
     * @return Table[]
     */
    protected function doGetMetaTables(DriverInterface $driver): array
    {
        $tableListStatement = $driver->query($this->tableList());
        $tableListStatement->setFetchMode(Statement::FETCH_NUM);
        $tables = [];
        foreach ($tableListStatement as $cols) {
            $tables[$cols[0]] = new Table([
                'name' => $cols[0],
            ]);
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
    protected function doGetMetaColumns(DriverInterface $driver, string $table): array
    {
        $indexListStatement = $driver->query($this->indexListOf($table));
        $indexListStatement->setFetchMode(Statement::FETCH_ASSOC);
        $indexes = [];
        foreach ($indexListStatement as $cols) {
            $indexInfoStatement = $driver->query($this->indexInfoOf($cols['name']));
            $indexInfo = $indexInfoStatement->fetch();
            $indexes[$indexInfo['name']] = $cols;
        }

        $tableInfoStatement = $driver->query($this->tableInfoOf($table));
        $tableInfoStatement->setFetchMode(Statement::FETCH_ASSOC);
        $columns = [];
        foreach ($tableInfoStatement as $cols) {
            $name = $cols['name'];
            $type = $cols['type'];
            $maxLength = null;
            $scale = null;
            if (preg_match('/^(.+)\((\d+),(\d+)/', $type, $matches)) {
                $type = $matches[1];
                $maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
                $scale = is_numeric($matches[3]) ? $matches[3] : -1;
            } elseif (preg_match('/^(.+)\((\d+)/', $type, $matches)) {
                $type = $matches[1];
                $maxLength = is_numeric($matches[2]) ? $matches[2] : -1;
            }
            $notNull = (bool)$cols['notnull'];
            $primaryKey = (bool)$cols['pk'];
            $uniqueKey = (array_key_exists($name, $indexes) && $indexes[$name]['unique'] === '1');
            $autoIncrement = ($primaryKey && strcasecmp($type, 'INTEGER') === 0);
            $binary = (strcasecmp($type, 'BLOB') === 0);
            $default = null;
            if (!$binary && isset($cols['dflt_value']) && strcasecmp($cols['dflt_value'], 'NULL') !== 0) {
                $default = $cols['dflt_value'];
            }
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
            ]);
        }

        return $columns;
    }

    /**
     * テーブル情報を取得するクエリを返します。
     *
     * @return string SQL
     */
    private function tableList(): string
    {
        return "SELECT name FROM sqlite_master WHERE type='table'";
    }

    /**
     * 指定テーブルのカラム情報を取得するクエリを返します。
     *
     * @param string $table テーブル名
     * @return string SQL
     */
    private function tableInfoOf(string $table): string
    {
        return sprintf('PRAGMA table_info(%s);', $table);
    }

    /**
     * 指定テーブルのインデックス情報を取得するクエリを返します。
     *
     * @param string $table テーブル名
     * @return string SQL
     */
    private function indexListOf(string $table): string
    {
        return sprintf('PRAGMA index_list(%s);', $table);
    }

    /**
     * 指定インデックスの情報を取得するクエリを返します。
     *
     * @param string $name インデックス名
     * @return string SQL
     */
    private function indexInfoOf(string $name): string
    {
        return sprintf('PRAGMA index_info(%s);', $name);
    }

}
