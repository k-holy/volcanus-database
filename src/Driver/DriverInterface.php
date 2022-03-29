<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

use Volcanus\Database\Dsn;
use Volcanus\Database\MetaData\MetaDataProcessorInterface;

/**
 * ドライバインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface DriverInterface
{

    /**
     * DSNをセットします。
     *
     * @param Dsn
     */
    public function setDsn(Dsn $dsn);

    /**
     * メタデータプロセッサをセットします。
     *
     * @param MetaDataProcessorInterface $metaDataProcessor
     */
    public function setMetaDataProcessor(MetaDataProcessorInterface $metaDataProcessor);

    /**
     * DBに接続します。
     *
     * @param Dsn $dsn
     * @return self
     */
    public function connect(Dsn $dsn): DriverInterface;

    /**
     * DBとの接続を解放します。
     *
     * @return bool
     */
    public function disconnect(): bool;

    /**
     * DBと接続中かどうかを返します。
     *
     * @return bool
     */
    public function connected(): bool;

    /**
     * ドライバ名を返します。
     *
     * @return string|null ドライバ名
     */
    public function getDriverName(): ?string;

    /**
     * ドライバに合ったメタデータプロセッサを生成します。
     *
     * @return MetaDataProcessorInterface
     */
    public function createMetaDataProcessor(): MetaDataProcessorInterface;

    /**
     * SQL実行準備を行い、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    public function prepare(string $query): StatementInterface;

    /**
     * SQLを実行し、ステートメントオブジェクトを返します。
     *
     * @param string $query SQL
     * @return StatementInterface
     */
    public function query(string $query): StatementInterface;

    /**
     * SQLを実行します。
     *
     * @param string $query SQL
     */
    public function execute(string $query);

    /**
     * 最後に発行(prepare/query/execute)したクエリを返します。
     *
     * @return string|null
     */
    public function getLastQuery(): ?string;

    /**
     * 最後に発生したエラーを返します。
     *
     * @return string|null
     */
    public function getLastError(): ?string;

    /**
     * 直近のinsert操作で生成されたIDを返します。
     *
     * @return mixed 実行結果
     */
    public function lastInsertId();

    /**
     * テーブルオブジェクトを配列で返します。
     *
     * @return Table[]
     */
    public function getMetaTables(): array;

    /**
     * 指定テーブルのカラムオブジェクトを配列で返します。
     *
     * @param string $table テーブル名
     * @return Column[]
     */
    public function getMetaColumns(string $table): array;

    /**
     * 文字列を引用符で適切にクォートして返します。
     *
     * @param string $value クォートしたい値
     * @return string クォート結果の文字列
     */
    public function quote(string $value): string;

    /**
     * LIKE演算子のエスケープ文字をセットします。
     *
     * @param string $char エスケープに使用する文字
     */
    public function setEscapeCharacter(string $char);

    /**
     * LIKE演算子のパターンとして使用する文字列をエスケープして返します。
     *
     * @param string $pattern パターン文字列
     * @return string エスケープされたパターン文字列
     */
    public function escapeLikePattern(string $pattern): string;

}
