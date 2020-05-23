<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

use Bic\Exception\EngineErrorException;
use FFI\CData;

/**
 * Class Enumerator
 */
class Enumerator
{
    /**
     * @var \FFI|object
     */
    private object $ctx;

    /**
     * @var string|Collection
     */
    private string $collection = Collection::class;

    /**
     * Enumerator constructor.
     *
     * @param \FFI|object $ctx
     */
    public function __construct($ctx)
    {
        $this->ctx = $ctx;
    }

    /**
     * @param string $collection
     * @return $this
     */
    public function through(string $collection): self
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param \FFI|object $ctx
     * @param string $type
     * @param callable $expr
     * @return Collection|CData[]
     */
    public static function collect($ctx, string $type, callable $expr): Collection
    {
        return (new static($ctx))->enumerate($type, $expr);
    }

    /**
     * @param string $type
     * @param callable $expr
     * @return Collection|CData[]
     */
    public function enumerate(string $type, callable $expr): Collection
    {
        $count = uint32();

        if (\is_int($status = $expr(\FFI::addr($count), null))) {
            EngineErrorException::assertVkResult($status);
        }

        if ($count->cdata === 0) {
            return $this->collection::make();
        }

        /** @noinspection StaticInvocationViaThisInspection */
        $array = $this->ctx->new($type . '[' . $count->cdata . ']');

        if (\is_int($status = $expr(\FFI::addr($count), $array))) {
            EngineErrorException::assertVkResult($status);
        }

        return ($this->collection)::fromCData($array, $count);
    }
}
