<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Driver;

/**
 * ステートメントインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface StatementInterface extends \IteratorAggregate
{

    /**
     * フェッチ後に実行するコールバックをセットします。
     *
     * @param callable $callback コールバック
     */
    public function setFetchCallback(callable $callback);

    /**
     * プリペアドステートメントを実行します。
     *
     * @param array|\Traversable $parameters パラメータ
     */
    public function execute($parameters = null);

    /**
     * このステートメントのデフォルトのフェッチモードを設定します。
     *
     * @param int $mode フェッチモード定数 (Statement::FETCH_**)
     * @param mixed $option フェッチモードのオプション引数
     * @param array|null $arguments Statement::FETCH_CLASS の場合のコンストラクタ引数
     */
    public function setFetchMode(int $mode, $option = null, array $arguments = null);

    /**
     * 結果セットから次の行を取得して返します。
     *
     * @return mixed
     */
    public function fetch();

    /**
     * 結果セットから全ての行を取得して配列で返します。
     *
     * @return array
     */
    public function fetchAll(): array;

}
