<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder\Adapter\Sqlite;

use Volcanus\Database\QueryBuilder\ExpressionBuilderInterface;

/**
 * SQLite Expressionビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteExpressionBuilder implements ExpressionBuilderInterface
{

	/**
	 * @var string 日付区切符（年月日）
	 */
	protected static $dateDelimiter = '-';

	/**
	 * @var string 日付区切符（時分秒）
	 */
	protected static $timeDelimiter = ':';

	/**
	 * @var string 日付区切符（年月日と時分秒）
	 */
	protected static $dateTimeDelimiter = ' ';

	/**
	 * 項目名/式により値を取得するSQL句を生成します。
	 *
	 * @param string 項目名/式
	 * @param string 別名
	 * @return string 値を取得するSQL句
	 */
	public function resultColumn($expr, $alias = null)
	{
		return (isset($alias)) ? $expr . ' AS "' . $alias . '"' : $expr;
	}

	/**
	 * コンストラクタ
	 *
	 * @param array 設定
	 */
	public function __construct(array $options = array())
	{
		if (isset($options['dateDelimiter'])) {
			static::$dateDelimiter = $options['dateDelimiter'];
		}
		if (isset($options['timeDelimiter'])) {
			static::$timeDelimiter = $options['timeDelimiter'];
		}
		if (isset($options['dateTimeDelimiter'])) {
			static::$dateTimeDelimiter = $options['dateTimeDelimiter'];
		}
	}

	/**
	 * 日付型の項目を書式化して取得するSQL句を生成します。
	 *
	 * @param string 項目名
	 * @return string 値を取得するSQL句
	 */
	public function asDate($name)
	{
		$format = '%Y' . self::$dateDelimiter
				. '%m' . self::$dateDelimiter
				. '%d';
		return sprintf("strftime('%s', %s)", $format, $name);
	}

	/**
	 * 日付時刻型の項目を書式化して取得するSQL句を生成します。
	 *
	 * @param string 項目名
	 * @return string 値を取得するSQL句
	 */
	public function asTimestamp($name)
	{
		$format = '%Y' . self::$dateDelimiter
				. '%m' . self::$dateDelimiter
				. '%d' . self::$dateTimeDelimiter
				. '%H' . self::$timeDelimiter
				. '%i' . self::$timeDelimiter
				. '%s';
		return sprintf("strftime('%s', %s)", $format, $name);
	}

}
