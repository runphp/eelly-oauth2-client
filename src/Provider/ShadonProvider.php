<?php

declare(strict_types=1);

/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Shadon\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\GenericProvider;
use Shadon\OAuth2\Client\Tool\AccessTokenCacheTrait;

/**
 * Shadon Provider.
 *
 * @author hehui<hehui@eelly.net>
 */
class ShadonProvider extends GenericProvider
{
    use AccessTokenCacheTrait;
}
