<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder\ParameterBuilder;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\QueryBuilder\QueryBuilder;

/**
 * Sqliteパラメータビルダ
 *
 * @author k_horii@rikcorp.jp
 */
class SqliteParameterBuilder extends AbstractParameterBuilder implements ParameterBuilderInterface
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
	 * @var array サポートするデータ型名
	 */
	protected static $types = array(
		'int'       => array('int', 'integer', 'tinyint', 'smallint', 'mediumint', 'bigint', 'int2', 'int4', 'int8'),
		'float'     => array('real', 'double', 'float'),
		'text'      => array('char', 'varchar', 'character', 'varying character', 'nchar', 'native character', 'nvarchar', 'text', 'clob'),
		'date'      => array('date'),
		'timestamp' => array('timestamp', 'datetime'),
	);

	/**
	 * @var \Volcanus\Database\Driver\DriverInterface
	 */
	protected $driver;

	/**
	 * コンストラクタ
	 *
	 * @param \Volcanus\Database\Driver\DriverInterface
	 */
	public function __construct(DriverInterface $driver)
	{
		$this->driver = $driver;
	}

	/**
	 * 値を可変長/固定長文字列を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toText($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_string($value) && strlen($value) === 0) {
			return 'NULL';
		}
		return $this->driver->quote($value);
	}

	/**
	 * 値を数値を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toInt($value, $type = null)
	{
		if (isset($type)) {
			if ($type === 'smallint' || $type === 'int2') {
				return $this->toInt2($value);
			} elseif ($type === 'bigint' || $type === 'int8') {
				return $this->toInt8($value);
			}
		}
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-2147483648';
			}
			if ($value === QueryBuilder::MAX) {
				return '2147483647';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を浮動小数点数を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toFloat($value, $type = null)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return (string)floatval($value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value == QueryBuilder::MIN) {
					return '-9223372036854775808';
			}
			if ($value == QueryBuilder::MAX) {
					return '9223372036854775807';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * データを date 型を表すSQLに変換します。
	 * @param string データ
	 * @return string 変換結果
	 */
	public function toDate($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value == QueryBuilder::NOW) {
				return "date('now')";
			}
			if ($value == QueryBuilder::MIN) {
				return "date('0000-01-01')";
			}
			if ($value == QueryBuilder::MAX) {
				return "date('9999-12-31')";
			}
			return sprintf("date('%s')", $value);
		}

		if (!isset($value[0])) {
			return 'NULL';
		}

		return sprintf("date('%04d%s%02d%s%02d')",
			(int)$value[0],
			static::$dateDelimiter,
			(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
			static::$dateDelimiter,
			(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1
		);
	}

	/**
	 * データを timestamp 型を表すSQLに変換します。
	 * @param string データ
	 * @return string 変換結果
	 */
	public function toTimestamp($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value == QueryBuilder::NOW) {
				return "datetime('now')";
			}
			if ($value == QueryBuilder::MIN) {
				return "datetime('0000-01-01 00:00:00')";
			}
			if ($value == QueryBuilder::MAX) {
				return "datetime('9999-12-31 23:59:59')";
			}
			return "datetime('{$value}')";
		}

		if (!isset($value[0])) {
			return 'NULL';
		}

		return sprintf("datetime('%04d%s%02d%s%02d%s%02d%s%02d%s%02d')",
			(int)$value[0],
			static::$dateDelimiter,
			(isset($value[1]) && $value[1] !== '') ? (int)$value[1] : 1,
			static::$dateDelimiter,
			(isset($value[2]) && $value[2] !== '') ? (int)$value[2] : 1,
			static::$dateTimeDelimiter,
			(isset($value[3]) && $value[3] !== '') ? (int)$value[3] : 0,
			static::$timeDelimiter,
			(isset($value[4]) && $value[4] !== '') ? (int)$value[4] : 0,
			static::$timeDelimiter,
			(isset($value[5]) && $value[5] !== '') ? (int)$value[5] : 0
		);
	}

	/**
	 * 値を2ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toInt2($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-32768';
			}
			if ($value === QueryBuilder::MAX) {
				return '32767';
			}
			return $value;
		}
		return (string)$value;
	}

	/**
	 * 値を8ビットの数値を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toInt8($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if (is_int($value) || is_float($value)) {
			return sprintf('%d', $value);
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value === QueryBuilder::MIN) {
				return '-9223372036854775808';
			}
			if ($value === QueryBuilder::MAX) {
				return '9223372036854775807';
			}
			return $value;
		}
		return (string)$value;
	}

}
