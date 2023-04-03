<?php /** @noinspection PhpUnused */

/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\MetaData;

use Volcanus\Database\AbstractPropertyAccessor;

/**
 * カラムクラス
 *
 * @property string $name
 * @property string $type
 * @property int $maxLength
 * @property int $scale
 * @property bool $unsigned
 * @property bool $binary
 * @property mixed $default
 * @property bool $notNull
 * @property bool $primaryKey
 * @property bool $uniqueKey
 * @property bool $autoIncrement
 * @property string $comment
 *
 * @author k.holy74@gmail.com
 */
class Column extends AbstractPropertyAccessor
{

    /**
     * @var string|null カラム名
     */
    protected ?string $name = null;

    /**
     * @var string|null データ型
     */
    protected ?string $type = null;

    /**
     * @var int|null 最大文字数
     */
    protected ?int $maxLength = null;

    /**
     * @var int|null 桁数
     */
    protected ?int $scale = null;

    /**
     * @var bool|null 符号なしかどうか
     */
    protected ?bool $unsigned = null;

    /**
     * @var bool|null バイナリデータかどうか
     */
    protected ?bool $binary = null;

    /**
     * @var mixed|null デフォルト値
     */
    protected mixed $default = null;

    /**
     * @var bool|null NOT NULL制約が付与されているかどうか
     */
    protected ?bool $notNull = null;

    /**
     * @var bool|null PRIMARY KEYかどうか
     */
    protected ?bool $primaryKey = null;

    /**
     * @var bool|null UNIQUE KEYかどうか
     */
    protected ?bool $uniqueKey = null;

    /**
     * @var bool|null AUTO INCREMENTかどうか
     */
    protected ?bool $autoIncrement = null;

    /**
     * @var string|null コメント
     */
    protected ?string $comment = null;

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
