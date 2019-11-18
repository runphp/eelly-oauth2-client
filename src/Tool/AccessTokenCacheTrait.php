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

use League\OAuth2\Client\Grant\Exception\InvalidGrantException;
use Phalcon\Cache\BackendInterface as CacheInterface;
use Symfony\Component\Cache\Adapter\PhpFilesAdapter;

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
        if (!\is_object($this->cache) || 'client_credentials' != $grant) {
            return parent::getAccessToken($grant, $options);
        }
        $params = [$grant, $options, $this->clientId];
        $key = getenv('APPLICATION_KEY');
        if (false != $key) {
            array_push($params, $key);
        }
        $keyName = $this->keyName('AccessTokenCacheTrait', __FUNCTION__, $params);
        $cachePool = new PhpFilesAdapter('eelly', 0, 'var/cache');
        $cacheItem = $cachePool->getItem($keyName);
        if ($cacheItem->isHit()) {
            $accessToken = $cacheItem->get();
        } else {
            try {
                $accessToken = parent::getAccessToken($grant, $options);
            } catch (InvalidGrantException $e) {
                $accessToken = $this->getCustomAccessToken($grant, $options);
            }
            $cacheItem->set($accessToken);
            $cacheItem->expiresAfter($accessToken->getExpires() - time() - 10 /* 提前10秒过期 */);
            $cachePool->save($cacheItem);
        }

        return $accessToken;
    }

    private function getCustomAccessToken($grant, array $options = [])
    {
        $class = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $grant)));
        $class = 'Shadon\\OAuth2\\Client\\Grant\\'.$class;
        $this->grantFactory->setGrant($grant, new $class());

        return parent::getAccessToken($grant, $options);
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
        return sprintf('%s_%s_%s', $class, $method, md5(json_encode($params)));
    }
}
