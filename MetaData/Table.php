<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright 2011-2013 k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\AbstractPropertyAccessor;

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

	/**
	 * コンストラクタ
	 *
	 * @param array | Traversable
	 */
	public function __construct($attributes = null)
	{
		if ($attributes !== null) {
			$this->properties($attributes);
		}
	}

}
