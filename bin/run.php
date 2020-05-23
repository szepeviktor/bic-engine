<?php

/**
 * This file is part of Bic Engine package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Bic\Application;

$app = new Application(__DIR__ . '/..');
$app->run();


