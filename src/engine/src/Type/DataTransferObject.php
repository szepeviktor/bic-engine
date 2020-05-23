<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

use Illuminate\Support\Arr;
use Illuminate\Support\Fluent;

/**
 * Class DataTransferObject
 */
abstract class DataTransferObject extends Fluent
{
    /**
     * @return array
     */
    public function __debugInfo(): array
    {
        return $this->toArray();
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        assert(\is_string($key));

        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return \vsprintf('dto(%s)#%d (%d) %s', [
            static::class,
            \spl_object_id($this),
            \count($this->attributes),
            \json_encode($this->toArray(), \JSON_PRETTY_PRINT | \JSON_THROW_ON_ERROR),
        ]);
    }
}
