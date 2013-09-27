<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder;

use Volcanus\Database\QueryBuilder\QueryBuilder;
use Volcanus\Database\QueryBuilder\ParameterBuilder\SqliteParameterBuilder;

/**
 * Sqliteクエリビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteQueryBuilder implements QueryBuilderInterface
{

	/**
	 * @var Volcanus\Database\QueryBuilder\ParameterBuilder
	 */
	private $parameterBuilder;

	/**
	 * コンストラクタ
	 *
	 * @param Volcanus\Database\QueryBuilder\ParameterBuilder\SqliteParameterBuilder
	 */
	public function __construct(SqliteParameterBuilder $parameterBuilder)
	{
		$this->parameterBuilder = $parameterBuilder;
	}

	/**
	 * SELECT文にLIMIT値およびOFFSET値を付与して返します。
	 *
	 * @param string SQL
	 * @param int 最大取得件数
	 * @param int 取得開始行index
	 * @return string SQL
	 */
	public function selectLimit($sql, $limit = null, $offset = null)
	{
		if (isset($limit) && (int)$limit >= 0) {
			$sql .= ' LIMIT ' . $this->parameterBuilder->toInt($limit);
			if (isset($offset) && (int)$offset >= 0) {
				$sql .= ' OFFSET ' . $this->parameterBuilder->toInt($offset);
			}
		}
		return $sql;
	}

}
