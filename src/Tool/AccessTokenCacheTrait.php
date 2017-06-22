<?php
/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eelly\OAuth2\Client\Tool;

use Phalcon\Cache\BackendInterface as CacheInterface;

/**
 * @author hehui<hehui@eelly.net>
 */
trait AccessTokenCacheTrait
{
    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * set accessToken Cache.
     *
     * @param object $cache
     */
    public function setAccessTokenCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getAccessToken($grant, array $options = [])
    {
        if (!is_object($this->cache)) {
            return parent::getAccessToken($grant, $options);
        }
        $keyName = __CLASS__.':'.__FUNCTION__.':'.$grant;
        if (!$this->cache->exists($keyName)) {
            /**
             * @var \League\OAuth2\Client\Token\AccessToken $accessToken
             */
            $accessToken = parent::getAccessToken($grant, $options);
            $this->cache->save($keyName, $accessToken, $accessToken->getExpires() - time());
        } else {
            $accessToken = $this->cache->get($keyName);
        }

        return $accessToken;
    }
}
