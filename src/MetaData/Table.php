<?php
/**
 * Volcanus libraries for PHP
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\AbstractPropertyAccessor;

/**
 * テーブルクラス
 *
 * @property string $name
 * @property string $comment
 * @property array $columns
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
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        $this->initialize($properties);
    }

}
