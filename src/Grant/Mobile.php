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

namespace Shadon\OAuth2\Client\Grant;

use League\OAuth2\Client\Grant\AbstractGrant;

/**
 * Represents a resource owner password credentials grant.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-1.3.3 Resource Owner Password Credentials (RFC 6749, §1.3.3)
 */
class Mobile extends AbstractGrant
{
    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return 'mobile';
    }

    /**
     * {@inheritdoc}
     */
    protected function getRequiredRequestParameters()
    {
        return [
            'username',
            'code',
        ];
    }
}
