<?php

namespace Tastd\Bundle\CoreBundle\Facebook;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Tastd\Bundle\CoreBundle\Exception\Api\Facebook\FacebookException;


/**
 * Class FacebookClient
 *
 * @package Tastd\Bundle\CoreBundle\Facebook
 */
class FacebookClient
{
    protected $appId;
    protected $appSecret;
    /** @var FacebookSession $session */
    protected $session;

    /**
     * @param string $appId
     * @param string $appSecret
     */
    public function __construct($appId, $appSecret)
    {
        $this->appId = $appId;
        $this->appSecret = $appSecret;
        FacebookSession::setDefaultApplication($appId, $appSecret);
    }

    /**
     * @param string $accessToken
     *
     * @throws FacebookException
     */
    public function connect($accessToken)
    {
        $this->session = new FacebookSession($accessToken);
        try {
            $this->session->validate();
        } catch (FacebookRequestException $exception) {
            // Session not valid, Graph API returned an exception with the reason.
            throw new FacebookException($exception->getMessage());
        } catch (\Exception $exception) {
            // Graph API returned info, but it may mismatch the current app or have expired.
            throw new FacebookException($exception->getMessage());
        }
    }

    /**
     * getUserInfo
     *
     * @throws  FacebookException
     * @return GraphUser
     */
    public function getGraphUser()
    {
        $url = '/me?fields=id,email,first_name,last_name,birthday,permissions';
        try {
            /** @var GraphUser $userProfile */
            $userProfile = (new FacebookRequest($this->session, 'GET', $url))
                ->execute()->getGraphObject(GraphUser::className());

            return $userProfile;

        } catch (FacebookRequestException $e) {
            throw new FacebookException($e->getMessage());
        }
    }

    /**
     * getAvatar
     *
     * @throws  FacebookException
     * @return GraphObject
     */
    public function getAvatar()
    {
        try {
            $request = new FacebookRequest(
                $this->session,
                'GET',
                '/me/picture',
                array (
                    'redirect' => false,
                    //'height' => '200',
                    'type' => 'large',
                    //'width' => '200',
                )
            );
            $response = $request->execute();

            return $response->getGraphObject();
        } catch (FacebookRequestException $e) {
            throw new FacebookException($e->getMessage());
        }
    }

    /**
     * Loop through Facebook friend's pages and create an array with all ids
     *
     * @return array
     * @throws FacebookException
     */
    public function getFriendsIds() {
        try {
            $request = new FacebookRequest($this->session, 'GET', '/me/friends');
            return $this->handleFriendsRequest($request, array());
        } catch (FacebookRequestException $e) {
            throw new FacebookException($e->getMessage());
        }
    }

    /**
     * @param FacebookRequest $request
     *
     * @return array
     *
     * @throws FacebookRequestException
     */
    function handleFriendsRequest (FacebookRequest $request) {
        $response = $request->execute();
        $graphObject = $response->getGraphObject()->getProperty('data');
        $ids = $this->getFriendsIdsFromGraphObject($graphObject);
        $requestNextPage = $response->getRequestForNextPage();
        if ($requestNextPage) {
            $nextPageIds = $this->handleFriendsRequest($requestNextPage);
            $ids = array_merge($ids, $nextPageIds);
        }

        return $ids;
    }

    /**
     * @param $graphObject
     * @return array
     */
    function getFriendsIdsFromGraphObject($graphObject)
    {
        $ids = array();
        if (!$graphObject instanceof GraphObject ) {
            return $ids;
        }

        $facebookFriends = $graphObject->asArray();
        foreach ($facebookFriends as $facebookFriend) {
            $ids[] = $facebookFriend->id;
        };

        return $ids;
    }

    /**
     * @param $link
     * @param $message
     * @return mixed
     *
     * @throws FacebookException
     */
    public function publishLink($link, $message)
    {
        try {
            $response = (new FacebookRequest($this->session, 'POST', '/me/feed', array(
                    'link' => $link,
                    'message' => $message
                )
            ))->execute()->getGraphObject();
            return $response;

        } catch(FacebookRequestException $e) {
            throw new FacebookException();
        }
    }

    /**
     * @return string
     */
    public function getLongLivedToken()
    {
        $longLivedSession = $this->session->getLongLivedSession($this->appId, $this->appSecret);

        return $longLivedSession->getToken();
    }

}