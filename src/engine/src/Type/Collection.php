<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bic\Type;

use FFI\CData;
use Illuminate\Support\Collection as BaseCollection;

/**
 * Class Collection
 */
class Collection extends BaseCollection
{
    /**
     * @var CData[]
     */
    private array $types = [];

    /**
     * Collection constructor.
     *
     * @param array $items
     * @param CData ...$types
     */
    public function __construct($items = [], CData ...$types)
    {
        $this->types = $types;

        parent::__construct($items);
    }

    /**
     * @param CData $array
     * @param CData $size
     * @return static
     */
    public static function fromCData(CData $array, CData $size): self
    {
        $data = [];

        for ($i = 0; $i < $size->cdata; ++$i) {
            $data[] = $array[$i];
        }

        return new static($data, $array, $size);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        foreach ($this->types as $type) {
            \FFI::free(\FFI::addr($type));
        }
    }
}
