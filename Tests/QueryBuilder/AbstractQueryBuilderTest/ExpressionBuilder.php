<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Tests\QueryBuilder\AbstractQueryBuilderTest;

use Volcanus\Database\QueryBuilder\ExpressionBuilderInterface;

class ExpressionBuilder implements ExpressionBuilderInterface
{

	public function resultColumn($expr, $alias = null)
	{
		return (isset($alias)) ? $expr . ' AS "' . $alias . '"' : $expr;
	}

	public function asDate($name)
	{
		return sprintf("TO_CHAR(%s, 'YYYY-MM-DD')", $name);
	}

	public function asTimestamp($name)
	{
		return sprintf("TO_CHAR(%s, 'YYYY-MM-DD HH24:MI:SS')", $name);
	}

}
