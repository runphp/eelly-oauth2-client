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

namespace Shadon\OAuth2\Client\Tool;

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
    public function setAccessTokenCache(CacheInterface $cache): void
    {
        $this->cache = $cache;
    }

    public function getAccessToken($grant, array $options = [])
    {
        if (!is_object($this->cache)) {
            return parent::getAccessToken($grant, $options);
        }
        $params = [$grant, $options, $this->clientId];
        $key = getenv('APPLICATION_KEY');
        if (false != $key) {
            array_push($params, $key);
        }
        $keyName = $this->keyName(__CLASS__, __FUNCTION__, $params);
        $accessToken = $this->cache->get($keyName);
        if (empty($accessToken) || $accessToken->hasExpired()) {
            $accessToken = parent::getAccessToken($grant, $options);
            $this->cache->save($keyName, $accessToken, $accessToken->getExpires() - time() - 10 /* 提前10秒过期 */);
        }

        return $accessToken;
    }

    /**
     * 缓存key.
     *
     * @param string $class
     * @param string $method
     * @param array  $params
     *
     * @return string
     */
    private function keyName($class, $method, array $params)
    {
        return sprintf('%s:%s:%s', $class, $method, $this->createKeyWithArray($params));
    }

    private function createKeyWithArray(array $parameters)
    {
        $uniqueKey = [];

        foreach ($parameters as $key => $value) {
            if (is_scalar($value)) {
                $uniqueKey[] = $key.':'.$value;
            } elseif (is_array($value)) {
                $uniqueKey[] = $key.':['.$this->createKeyWithArray($value).']';
            } else {
                throw new \InvalidArgumentException('can not use cache annotation', 500);
            }
        }

        return implode(',', $uniqueKey);
    }
}
