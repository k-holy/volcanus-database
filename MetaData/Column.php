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
 * カラムクラス
 *
 * @author k.holy74@gmail.com
 */
class Column extends AbstractPropertyAccessor
{

	/**
	 * @var string カラム名
	 */
	protected $name;

	/**
	 * @var string データ型
	 */
	protected $type;

	/**
	 * @var int 最大文字数
	 */
	protected $maxLength;

	/**
	 * @var int 桁数
	 */
	protected $scale;

	/**
	 * @var bool バイナリデータかどうか
	 */
	protected $binary;

	/**
	 * @var mixed デフォルト値
	 */
	protected $default;

	/**
	 * @var bool NOT NULL制約が付与されているかどうか
	 */
	protected $notNull;

	/**
	 * @var bool PRIMARY KEYかどうか
	 */
	protected $primaryKey;

	/**
	 * @var bool UNIQUE KEYかどうか
	 */
	protected $uniqueKey;

	/**
	 * @var bool AUTO INCREMENTかどうか
	 */
	protected $autoIncrement;

	/**
	 * @var string コメント
	 */
	protected $comment;

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
