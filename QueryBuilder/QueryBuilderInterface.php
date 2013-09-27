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
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string SQL
	 * @param int 最大取得件数
	 * @param int 取得開始行index
	 * @return string SQL
	 */
	public function selectLimit($sql, $limit = null, $offset = null);

}
