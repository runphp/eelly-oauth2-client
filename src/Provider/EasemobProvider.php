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

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use Shadon\OAuth2\Client\Tool\AccessTokenCacheTrait;

/**
 * Easemob provider.
 *
 * @author hehui<hehui@eelly.net>
 */
class EasemobProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;
    use AccessTokenCacheTrait;

    private const URI = 'https://a1.easemob.com';

    protected $orgName;

    protected $appName;

    /**
     * @var string
     */
    private $responseError = 'error';

    /**
     * @var string
     */
    private $responseCode;

    /**
     * @var string
     */
    private $responseResourceOwnerId = 'id';

    /**
     * @param array $options
     * @param array $collaborators
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->orgName = $options['orgName'];
        $this->appName = $options['appName'];
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getBaseAuthorizationUrl()
     */
    public function getBaseAuthorizationUrl()
    {
        return self::URI.'/'.$this->orgName.'/'.$this->appName;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getBaseAccessTokenUrl()
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return self::URI.'/'.$this->orgName.'/'.$this->appName.'/token';
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getResourceOwnerDetailsUrl()
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getAccessTokenOptions()
     */
    protected function getAccessTokenOptions(array $params)
    {
        $options = ['headers' => ['content-type' => 'application/json']];

        if (self::METHOD_POST === $this->getAccessTokenMethod()) {
            $options['body'] = json_encode($params);
        }

        return $options;
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::getDefaultScopes()
     */
    protected function getDefaultScopes(): void
    {
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::checkResponse()
     */
    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            $code = $this->responseCode ? $data[$this->responseCode] : 0;

            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \League\OAuth2\Client\Provider\AbstractProvider::createResourceOwner()
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GenericResourceOwner($response, $this->responseResourceOwnerId);
    }
}
