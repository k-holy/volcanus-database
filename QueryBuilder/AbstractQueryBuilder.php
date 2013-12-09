<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\QueryBuilder;

use Volcanus\Database\QueryBuilder\ExpressionBuilderInterface;
use Volcanus\Database\QueryBuilder\ParameterBuilderInterface;

/**
 * クエリビルダ抽象クラス
 *
 * @author k_horii@rikcorp.jp
 */
abstract class AbstractQueryBuilder
{

	/**
	 * @var Volcanus\Database\QueryBuilder\ExpressionBuilderInterface
	 */
	protected $expressionBuilder;

	/**
	 * @var Volcanus\Database\QueryBuilder\ParameterBuilderInterface
	 */
	protected $parameterBuilder;

	/**
	 * @param Volcanus\Database\QueryBuilder\ExpressionBuilderInterface
	 */
	protected function setExpressionBuilder(ExpressionBuilderInterface $expressionBuilder)
	{
		$this->expressionBuilder = $expressionBuilder;
	}

	/**
	 * @param Volcanus\Database\QueryBuilder\ExpressionBuilderInterface
	 */
	protected function setParameterBuilder(ParameterBuilderInterface $parameterBuilder)
	{
		$this->parameterBuilder = $parameterBuilder;
	}

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
			throw new \InvalidArgumentException(
				sprintf('Unsupported type:"%s"', $type)
			);
		}
		$methodName = 'to' . ucfirst($sqlType);
		if (!method_exists($this->parameterBuilder, $methodName)) {
			throw new \InvalidArgumentException(
				sprintf('Method not exists, Unsupported type:"%s"', $type)
			);
		}
		return $this->parameterBuilder->$methodName($value, $type);
	}

	/**
	 * データ型に合わせて項目を別名で取得するSQL句を生成します。
	 *
	 * @param string 項目名
	 * @param string データ型
	 * @param string 別名
	 * @return string SQL句
	 */
	public function expression($expr, $type = null, $alias = null)
	{
		if (isset($type)) {
			$sqlType = $this->parameterType($type);
			if (!$sqlType) {
				throw new \InvalidArgumentException(
					sprintf('Unsupported type:"%s"', $type)
				);
			}
			$methodName = 'as' . ucfirst($sqlType);
			if (method_exists($this->expressionBuilder, $methodName)) {
				if (!isset($alias)) {
					$alias = $expr;
				}
				$expr = $this->expressionBuilder->$methodName($expr, $type);
			}
		}
		return $this->expressionBuilder->resultColumn($expr, $alias);
	}

	/**
	 * Like演算子のパターンとして使用する文字列をエスケープして返します。
	 *
	 * @param string 抽出対象項目名
	 * @param string エスケープに使用する文字
	 * @return string エスケープされた文字列
	 */
	public function escapeLikePattern($pattern, $escapeChar = '\\')
	{
		$transTable = array(
			'_' => "{$escapeChar}_" ,
			'%' => "{$escapeChar}%" ,
			"{$escapeChar}" => "{$escapeChar}{$escapeChar}" ,
		);
		return strtr($pattern, $transTable);
	}

}
