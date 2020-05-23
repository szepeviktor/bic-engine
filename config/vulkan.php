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
    | Vulkan Version
    |--------------------------------------------------------------------------
    |
    | The version of the Vulkan API required for the application to work.
    |
    */

    'version' => env('VK_VERSION', '1.1.0'),

    /*
    |--------------------------------------------------------------------------
    | Vulkan Layers
    |--------------------------------------------------------------------------
    |
    | Vulkan layers is a set of API call interceptors that allow you to debug,
    | validate, profile (and so on) your application.
    |
    |   - "required": List of required layers.
    |
    |   - "optional": List of optional layers that will be added to the
    |                 application only if they are available and supported
    |                 by drivers, GPU or OS.
    |
    */

    'layers' => [

        'required' => [],

        'optional' => [
            /**
             * The main, comprehensive Khronos validation layer.
             *
             * Vulkan is an Explicit API, enabling direct control over how
             * GPUs actually work. By design, minimal error checking is
             * done inside a Vulkan driver. Applications have full control
             * and responsibility for correct operation. Any errors in how
             * Vulkan is used can result in a crash. The Khronos Valiation
             * Layer can be enabled to assist development by enabling
             * developers to verify their applications correctly use the
             * Vulkan API.
             *
             * Note: Only available if the Vulkan SDK is installed.
             *
             * @see https://vulkan.lunarg.com/doc/sdk/1.2.135.0/linux/validation_layers.html
             */
            env('APP_DEBUG', false) ? 'VK_LAYER_KHRONOS_validation' : null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Vulkan Extensions
    |--------------------------------------------------------------------------
    |
    | Extensions may add new commands, types, and tokens, or collectively
    | "objects", to the Vulkan API.
    |
    |   - "required": List of required extensions.
    |
    |   - "optional": List of optional extensions that will be added to the
    |                 application only if they are available and supported
    |                 by drivers, GPU or OS.
    |
    */

    'extensions' => [

        'required' => [

        ],

        'optional' => [],

    ],
];
