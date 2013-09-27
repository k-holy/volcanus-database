<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder\ParameterBuilder;

/**
 * パラメータビルダインタフェース
 *
 * @author k.holy74@gmail.com
 */
interface ParameterBuilderInterface
{

	/**
	 * 型名から、SQLパラメータ用の型名を返します。
	 *
	 * @param string $type 型名 ($typesフィールド参照)
	 * @return string SQLパラメータ用の型名
	 */
	public function parameterType($type);

	/**
	 * 値を指定した型に応じたSQLパラメータ値に変換します。
	 *
	 * @param string データ
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function parameter($value, $type);

	/**
	 * 値を可変長/固定長文字列を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toText($value);

	/**
	 * 値を数値を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toInt($value, $type = null);

	/**
	 * 値を浮動小数点数を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function toFloat($value, $type = null);

	/**
	 * 値を日付を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toDate($value);

	/**
	 * 値を日時を表すSQLパラメータ値に変換します。
	 *
	 * @param string 値
	 * @return string 変換結果
	 */
	public function toTimestamp($value);

}
