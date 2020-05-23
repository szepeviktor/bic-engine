<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Version
 */
class Version implements Arrayable
{
    /**
     * @var int
     */
    public int $major;

    /**
     * @var int
     */
    public int $minor;

    /**
     * @var int
     */
    public int $patch;

    /**
     * Version constructor.
     *
     * @param int $major
     * @param int $minor
     * @param int $patch
     */
    public function __construct(int $major = 1, int $minor = 0, int $patch = 0)
    {
        $this->major = $major;
        $this->minor = $minor;
        $this->patch = $patch;
    }

    /**
     * @param mixed $version
     * @return static
     */
    public static function new($version): self
    {
        switch (true) {
            case \is_string($version):
                return self::fromString($version);

            case \is_int($version):
                return self::fromInt($version);

            case \is_float($version):
                return self::fromFloat($version);

            case $version instanceof self:
                return $version;

            default:
                throw new \InvalidArgumentException('Invalid version type');
        }
    }

    /**
     * @param int $version
     * @return static
     */
    public static function fromInt(int $version): self
    {
        return new static(
            static::major($version),
            static::minor($version),
            static::patch($version),
        );
    }

    /**
     * @param float $version
     * @return static
     * @noinspection PrintfScanfArgumentsInspection
     */
    public static function fromFloat(float $version): self
    {
        \assert($version >= 0.0, 'Version MUST be greater than 0, but ' . $version . ' given');

        return new static(...\sscanf((string)$version, '%d.%d'));
    }

    /**
     * @param string $version
     * @return static
     */
    public static function fromString(string $version): self
    {
        $chunks = \explode('.', $version);
        $chunks = \array_map(fn (string $v): int => (int)$v, $chunks);

        return new static(...$chunks);
    }

    /**
     * @return int
     */
    public function toInt(): int
    {
        return static::make($this->major, $this->minor, $this->patch);
    }

    /**
     * @return float
     */
    public function toFloat(): float
    {
        return (float)"$this->major.$this->minor";
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return \vsprintf('%d.%d.%d', [
            $this->major,
            $this->minor,
            $this->patch,
        ]);
    }

    /**
     * @param int $major
     * @param int $minor
     * @param int $patch
     * @return int
     */
    public static function make(int $major = 1, int $minor = 0, int $patch = 0): int
    {
        return $major << 22 | $minor << 12 | $patch;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function major(int $version): int
    {
        return $version >> 22;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function minor(int $version): int
    {
        return $version >> 12 & 0x3ff;
    }

    /**
     * @param int $version
     * @return int
     */
    public static function patch(int $version): int
    {
        return $version & 0xfff;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
