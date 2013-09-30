<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder;

use Volcanus\Database\Driver\StatementInterface;

/**
 * クエリビルダインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface QueryBuilderInterface
{

	/**
	 * 値を指定した型に応じたSQLパラメータ値に変換します。
	 *
	 * @param string データ
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function parameter($value, $type);

	/**
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string SELECT文
	 * @param int 最大取得件数
	 * @param int 取得開始行index
	 * @return string SQL
	 */
	public function selectLimit($sql, $limit = null, $offset = null);

	/**
	 * SELECT文を元に件数を返すクエリを生成して返します。
	 *
	 * @param string SELECT文
	 * @return string SQL
	 */
	public function selectCount($sql);

}
