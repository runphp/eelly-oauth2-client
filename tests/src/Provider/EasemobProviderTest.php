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

namespace Eelly\OAuth2\Client\Provider;

use Eelly\Cache\Backend\Predis;
use League\OAuth2\Client\Token\AccessToken;
use Phalcon\Cache\Frontend\Igbinary;
use PHPUnit\Framework\TestCase;

/**
 * @author hehui<hehui@eelly.net>
 */
class EasemobProviderTest extends TestCase
{
    /**
     * @var EasemobProvider
     */
    private $provider;

    /**
     * {@inheritdoc}
     *
     * @see \PHPUnit\Framework\TestCase::setUp()
     */
    public function setUp(): void
    {
        $options = [
            'clientId'     => 'YXA6UR5jYHMdEeWVfi1kLYliWw',
            'clientSecret' => 'YXA61KlUhrvYXNTT_aymCx0bPDfoQMs',
            'orgName'      => 'www-eelly-com',
            'appName'      => 'buyerdevelopment',
            'signResponse' => 'syn32i94453c7a5', // 输出签名
            'signRequest'  => 'knbxouvb0x0xrdc',  // 输入签名
        ];
        $this->provider = new EasemobProvider($options);
    }

    public function testGetAccessToken(): void
    {
        $accessToken = $this->provider->getAccessToken('client_credentials');
        $this->assertInstanceOf(AccessToken::class, $accessToken);

        $redisServer = [
            'parameters' => [
                'tcp://172.18.107.120:7000',
                'tcp://172.18.107.120:7001',
                'tcp://172.18.107.120:7002',
                'tcp://172.18.107.120:7003',
                'tcp://172.18.107.120:7004',
                'tcp://172.18.107.120:7005',
            ],
            'options' => [
                'connections' => [
                    'tcp'  => 'Predis\Connection\PhpiredisStreamConnection',  // PHP stream resources
                    'unix' => 'Predis\Connection\PhpiredisSocketConnection',  // ext-socket resources
                ],
                'cluster' => 'redis',
            ],
            'statsKey' => '_PHCR_MEMBER_STATS',
        ];
        $cache = new Predis(new Igbinary(), $redisServer);
        $this->provider->setAccessTokenCache($cache);
        // 测试缓存
        $accessToken1 = $this->provider->getAccessToken('client_credentials');
        $accessToken2 = $this->provider->getAccessToken('client_credentials');
        $this->assertEquals($accessToken1->getToken(), $accessToken2->getToken());
    }
}
