<?php

namespace Tastd\Bundle\CoreBundle\Entity\Factory;
use Tastd\Bundle\CoreBundle\Entity\Credential;
use Tastd\Bundle\CoreBundle\Exception\Api\Facebook\FacebookException;
use Tastd\Bundle\CoreBundle\Facebook\FacebookClient;
use Tastd\Bundle\CoreBundle\Key\CredentialProvider;

/**
 * Class CredentialFactory
 *
 * @package Tastd\Bundle\CoreBundle\Entity\Factory
 */
class CredentialFactory
{
    /** @var FacebookClient */
    protected $facebookClient;

    /**
     * @param FacebookClient $facebookClient
     */
    public function __construct(FacebookClient $facebookClient)
    {
        $this->facebookClient = $facebookClient;
    }

    /**
     * @param $token
     * @return Credential
     *
     * @throws FacebookException
     */
    public function createFromFacebookToken($token)
    {
        $credential = new Credential();

        return $this->buildFromFacebookToken($credential, $token);
    }

    /**
     * @param Credential $credential
     * @param $token
     * @return Credential
     */
    public function patchFromFacebookToken(Credential $credential, $token)
    {
        return $this->buildFromFacebookToken($credential, $token);
    }

    /**
     * @param Credential $credential
     * @param $token
     * @return Credential
     * @throws FacebookException
     */
    private function buildFromFacebookToken(Credential $credential, $token)
    {
        $this->facebookClient->connect($token);
        $graphUser = $this->facebookClient->getGraphUser();
        $credential->setToken($token);
        $credential->setExternalId($graphUser->getProperty('id'));
        $permissions = array();
        $permissionsStdClass = $graphUser->getProperty('permissions')->asArray();
        foreach ($permissionsStdClass as $stdClass) {
            $permissions[] = (array)$stdClass;
        }
        $credential->setPermissions($permissions);
        $credential->setProvider(CredentialProvider::FACEBOOK);

        return $credential;
    }

}