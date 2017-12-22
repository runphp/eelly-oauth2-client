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
use Psr\Http\Message\ResponseInterface;
use Shadon\OAuth2\Client\Tool\AccessTokenCacheTrait;

/**
 * Eelly Provider.
 *
 * @author hehui<hehui@eelly.net>
 */
class EellyProvider extends GenericProvider
{
    use AccessTokenCacheTrait;

    /**
     * @param ResponseInterface $response
     *
     * @return array
     */
    protected function parseResponse(ResponseInterface $response)
    {
        if (500 == $response->getStatusCode()) {
            $arr = parent::parseResponse($response);

            throw new \RuntimeException($arr['error']);
        } else {
            return parent::parseResponse($response);
        }
    }
}
