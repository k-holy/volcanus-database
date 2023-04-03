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
use Volcanus\Database\MetaData\Table;
use Volcanus\Database\MetaData\Column;
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
     * @throws \Exception
     */
    protected function doGetMetaTables(DriverInterface $driver): array
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
     * @param DriverInterface $driver データベースドライバ
     * @param string $table テーブル名
     * @return Column[]
     * @throws \Exception
     */
    protected function doGetMetaColumns(DriverInterface $driver, string $table): array
    {
        $columnsStatement = $driver->query($this->showFullColumnsFrom($table));
        $columnsStatement->setFetchMode(Statement::FETCH_ASSOC);
        $columns = [];
        foreach ($columnsStatement->getIterator() as $cols) {
            $name = $cols['Field'];
            $type = $cols['Type'];
            $maxLength = null;
            $scale = null;
            $unsigned = false;
            if (preg_match('/^(enum)\((.*)\)$/i', $type, $matches)) {
                $type = $matches[1];
                $zlen = max(array_map('strlen', explode(',', $matches[2]))) - 2;
                $maxLength = ($zlen > 0) ? $zlen : 1;
            } elseif (preg_match('/^([a-z0-9]+)\((\d+),(\d+)/i', $type, $matches)) {
                $type = $matches[1];
                $maxLength = ctype_digit($matches[2]) ? $matches[2] : -1;
                $scale = ctype_digit($matches[3]) ? $matches[3] : -1;
            } elseif (preg_match('/^([a-z0-9]+)\((\d+)\)( unsigned)?$/i', $type, $matches)) { // < MySQL8.0
                $type = $matches[1];
                $maxLength = ctype_digit($matches[2]) ? $matches[2] : -1;
                $unsigned = isset($matches[3]);
            } elseif (preg_match('/^([a-z0-9]+)( unsigned)?$/i', $type, $matches)) { // >= MySQL8.0
                $type = $matches[1];
                $unsigned = isset($matches[2]);
            }
            $notNull = ($cols['Null'] !== 'YES');
            $primaryKey = ($cols['Key'] === 'PRI');
            $uniqueKey = ($cols['Key'] === 'UNI');
            $autoIncrement = (isset($cols['Extra']) && str_contains($cols['Extra'], 'auto_increment'));
            $binary = (str_contains($type, 'blob'));
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
                'unsigned' => $unsigned,
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
    private function tableList(): string
    {
        return 'SHOW TABLES;';
    }

    /**
     * 指定テーブルのカラム情報を取得するクエリを返します。
     *
     * @param string $table テーブル名
     * @return string SQL
     */
    private function showFullColumnsFrom(string $table): string
    {
        return sprintf('SHOW FULL COLUMNS FROM %s', $table);
    }

}
