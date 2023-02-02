<?php
/**
 * Volcanus libraries for PHP 8.1~
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
     * @var string|null テーブル名
     */
    protected ?string $name = null;

    /**
     * @var string|null コメント
     */
    protected ?string $comment = null;

    /**
     * @var array|null カラム配列
     */
    protected ?array $columns = null;

    /**
     * コンストラクタ
     *
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);
    }

}
