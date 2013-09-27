<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder\ParameterBuilder;

/**
 * パラメータビルダ抽象クラス
 *
 * @author k_horii@rikcorp.jp
 */
abstract class AbstractParameterBuilder
{

	/**
	 * 型名から、SQLパラメータ用の型名を返します。
	 *
	 * @param string $type 型名 ($typesフィールド参照)
	 * @return string SQLパラメータ用の型名
	 */
	public function parameterType($type)
	{
		$type = strtolower($type);
		foreach (static::$types as $parameterType => $parameterTypes) {
			if ($type === $parameterType || in_array($type, $parameterTypes)) {
				return $parameterType;
			}
		}
		return false;
	}

	/**
	 * 値を指定した型に応じたSQLパラメータ値に変換します。
	 *
	 * @param string データ
	 * @param string 型名 ($typesフィールド参照)
	 * @return string 変換結果
	 */
	public function parameter($value, $type)
	{
		$sqlType = $this->parameterType($type);
		if (!$sqlType) {
			throw new \RuntimeException(
				sprintf('Unsupported type:"%s"', $type)
			);
		}
		$methodName = 'to' . ucfirst($sqlType);
		if (!method_exists($this, $methodName)) {
			throw new \RuntimeException(
				sprintf('Method not exists, Unsupported type:"%s"', $type)
			);
		}
		return $this->$methodName($value, $type);
	}

}
