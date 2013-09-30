<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder\Adapter\Sqlite;

use Volcanus\Database\QueryBuilder\ParameterBuilderInterface;
use Volcanus\Database\QueryBuilder\AbstractParameterBuilder;

use Volcanus\Database\Driver\DriverInterface;
use Volcanus\Database\QueryBuilder\QueryBuilder;

/**
 * SQLite パラメータビルダ
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
				return parent::toInt2($value);
			} elseif ($type === 'bigint' || $type === 'int8') {
				return parent::toInt8($value);
			}
		}
		return parent::toInt4($value);
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
	 * 値を真偽値を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toBool($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}
		if ($value === QueryBuilder::MIN) {
			return '0';
		}
		if ($value === QueryBuilder::MAX) {
			return '1';
		}
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
		}
		return sprintf('%d', (bool)$value ? 1 : 0);
	}

	/**
	 * 値を日付を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toDate($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		// Unix Timestamp
		if (is_int($value) || is_float($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		// DateTime
		if ($value instanceof \DateTime) {
			return sprintf("date('%s')",
				$value->format(sprintf('Y%sm%sd',
					static::$dateDelimiter,
					static::$dateDelimiter
				))
			);
		}

		// String of a date
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value == QueryBuilder::NOW) {
				return "date('now')";
			}
			if ($value == QueryBuilder::MIN) {
				return sprintf("date('%04d%s%02d%s%02d')",
					0,
					static::$dateDelimiter,
					1,
					static::$dateDelimiter,
					1
				);
			}
			if ($value == QueryBuilder::MAX) {
				return sprintf("date('%04d%s%02d%s%02d')",
					9999,
					static::$dateDelimiter,
					12,
					static::$dateDelimiter,
					31
				);
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
	 * 値を日時を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toTimestamp($value)
	{
		if (!isset($value)) {
			return 'NULL';
		}

		// Unix Timestamp
		if (is_int($value) || is_float($value)) {
			$value = new \DateTime(sprintf('@%d', $value));
			$value->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		}

		// DateTime
		if ($value instanceof \DateTime) {
			return sprintf("datetime('%s')",
				$value->format(sprintf('Y%sm%sd%sH%si%ss',
					static::$dateDelimiter,
					static::$dateDelimiter,
					static::$dateTimeDelimiter,
					static::$timeDelimiter,
					static::$timeDelimiter
				))
			);
		}

		// Datetime string
		if (is_string($value)) {
			if (strlen($value) === 0) {
				return 'NULL';
			}
			if ($value == QueryBuilder::NOW) {
				return "datetime('now')";
			}
			if ($value == QueryBuilder::MIN) {
				return sprintf("datetime('%04d%s%02d%s%02d%s%02d%s%02d%s%02d')",
					0,
					static::$dateDelimiter,
					1,
					static::$dateDelimiter,
					1,
					static::$dateTimeDelimiter,
					0,
					static::$timeDelimiter,
					0,
					static::$timeDelimiter,
					0
				);
			}
			if ($value == QueryBuilder::MAX) {
				return sprintf("datetime('%04d%s%02d%s%02d%s%02d%s%02d%s%02d')",
					9999,
					static::$dateDelimiter,
					12,
					static::$dateDelimiter,
					31,
					static::$dateTimeDelimiter,
					23,
					static::$timeDelimiter,
					59,
					static::$timeDelimiter,
					59
				);
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

}
