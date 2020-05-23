<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default UI Driver
    |--------------------------------------------------------------------------
    |
    | This parameter controls the default user interface driver that is used
    | to work with UI. This functionality is required for working with windows,
    | events, event loop and other similar functionality.
    |
    | Supported:
    |   - \Bic\UI\GLFW3\Driver::class
    |   - null (for auto selection)
    |
    */

    'driver' => null,

    /*
    |--------------------------------------------------------------------------
    | Default UI Window Name
    |--------------------------------------------------------------------------
    |
    | Here you can specify which of the windows below you want to use by
    | default for all work with the interface. Of course, you can use multiple
    | windows at the same time using the window manager.
    |
    */

    'window' => 'default',

    'windows' => [

        'default' => [
            'name'   => null,
            'width'  => 640,
            'height' => 480,
        ],

    ],
];
