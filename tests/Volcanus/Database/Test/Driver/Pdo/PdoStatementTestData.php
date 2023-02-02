<?php
/** @noinspection PhpUnusedPrivateFieldInspection */
/** @noinspection PhpPropertyOnlyWrittenInspection */

/**
 * Volcanus libraries for PHP 8.1~
 *
 * @copyright k-holy <k.holy74@gmail.com>
 * @license The MIT License (MIT)
 */

namespace Volcanus\Database\Test\Driver\Pdo;

/**
 * TestData for PdoStatement
 *
 * @property string $userId
 * @property string $userName
 * @property string $birthday
 * @property string $createdAt
 * @property string $now

 * @author k.holy74@gmail.com
 */
class PdoStatementTestData
{
    /**
     * @var string|null
     */
    private ?string $userId = null;

    /**
     * @var string|null
     */
    private ?string $userName = null;

    /**
     * @var string|null Y-m-d
     */
    private ?string $birthday = null;

    /**
     * @var string|null タイムスタンプ値
     */
    private ?string $createdAt = null;

    /**
     * @var \DateTime|null 現在日時
     */
    private ?\DateTime $now = null;

    /**
     * __construct()
     *
     * @param array|null $properties プロパティの配列
     */
    public function __construct(array $properties = null)
    {
        if ($properties !== null) {
            foreach (array_keys(get_object_vars($this)) as $name) {
                $this->{$name} = null;
                if (array_key_exists($name, $properties)) {
                    $this->__set($name, $properties[$name]);
                    unset($properties[$name]);
                }
            }
            if (count($properties) !== 0) {
                throw new \InvalidArgumentException(
                    sprintf('Not supported properties [%s]',
                        implode(',', array_keys($properties))
                    )
                );
            }
        }
    }

    /**
     * __isset
     *
     * @param string $name
     * @return bool
     */
    public function __isset(string $name)
    {
        return (property_exists($this, $name) && $this->{$name} !== null);
    }

    /**
     * __get
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        if (method_exists($this, 'get' . ucfirst($name))) {
            return $this->{'get' . ucfirst($name)}();
        }
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException(
                sprintf('The property "%s" does not exists.', $name)
            );
        }
        return $this->{$name};
    }

    /**
     * __set
     *
     * @param string $name
     * @param mixed $value
     * @throws \InvalidArgumentException
     */
    public function __set(string $name, mixed $value)
    {
        if (method_exists($this, 'set' . ucfirst($name))) {
            $this->{'set' . ucfirst($name)}($value);
        }
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException(
                sprintf('The property "%s" does not exists.', $name)
            );
        }
        $this->{$name} = $value;
    }

    /**
     * __unset
     *
     * @param string $name
     * @throws \InvalidArgumentException
     */
    public function __unset(string $name)
    {
        if (!property_exists($this, $name)) {
            throw new \InvalidArgumentException(
                sprintf('The property "%s" does not exists.', $name)
            );
        }
        $this->{$name} = null;
    }

    /**
     * 現在日時をセットします。
     *
     * @param \DateTime $now
     * @return void
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function setNow(\DateTime $now): void
    {
        $this->now = $now;
    }

    /**
     * 年齢を返します。
     *
     * @return int|null
     * @noinspection PhpUnused
     */
    public function getAge(): ?int
    {
        if ($this->birthday !== null && $this->now !== null) {
            $birthday = \DateTime::createFromFormat('Y-m-d', $this->birthday);
            return (int)(((int)$this->now->format('Ymd') - (int)$birthday->format('Ymd')) / 10000);
        }
        return null;
    }

}
