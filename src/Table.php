<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database;

/**
 * テーブルクラス
 *
 * @author k.holy74@gmail.com
 */
class Table extends AbstractPropertyAccessor
{

	/**
	 * @var string テーブル名
	 */
	protected $name;

	/**
	 * @var string コメント
	 */
	protected $comment;

	/**
	 * @var array カラム配列
	 */
	protected $columns;

}
