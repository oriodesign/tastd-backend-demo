<?php

namespace Tastd\Bundle\CoreBundle\Manager;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use FOS\UserBundle\Util\TokenGenerator;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tastd\Bundle\CoreBundle\Entity\Credential;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\AuthTokenExpiredException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\InvalidAuthTokenException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\InvalidCredentialException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\InvalidRefreshTokenException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\NonUniqueUserException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\RefreshTokenExpiredException;
use Tastd\Bundle\CoreBundle\Exception\Api\Auth\UserNotFoundException;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class UserManager
 *
 * @package Tastd\Bundle\CoreBundle\Manager
 */
class UserManager extends BaseUserManager
{
    /** @var TokenGenerator $tokenGenerator */
    protected $tokenGenerator;
    /** @var UserRepository $repository */
    protected $repository;

    /**
     * @param string $email
     *
     * @return array
     */
    public function getUsersByEmailWithCredential($email)
    {
        return $this->repository->getUsersByEmailWithCredential($email);
    }

    /**
     * @param TokenGenerator $tokenGenerator
     */
    public function setTokenGenerator(TokenGenerator $tokenGenerator)
    {
        $this->tokenGenerator = $tokenGenerator;
    }

    /**
     * @param string $provider
     * @param string $token
     *
     * @throws InvalidCredentialException
     *
     * @return User
     */
    public function findUserByCredentialToken($provider, $token)
    {
        try {
            return $this->repository->getSingleUserByCredentialToken($provider, $token);
        } catch (NonUniqueResultException $e) {
            throw new InvalidCredentialException();
        } catch (NoResultException $e) {
            throw new InvalidCredentialException();
        }
    }

    /**
     * @param string $provider
     * @param string $id
     *
     * @throws NonUniqueUserException
     * @throws UserNotFoundException
     *
     * @return User
     */
    public function findUserByExternalId($provider, $id)
    {
        try {
            return $this->repository->getUserByCredentialExternalId($provider, $id);
        } catch (NonUniqueResultException $e) {
            throw new NonUniqueUserException('Multiple users with this credential external user id');
        } catch (NoResultException $e) {
            throw new UserNotFoundException('User Not found with this external user id');
        }
    }

    /**
     * @param string $authToken
     *
     * @throws AuthTokenExpiredException
     * @throws InvalidAuthTokenException
     * @return User
     */
    public function findUserByAuthToken($authToken)
    {
        $user = $this->findUserBy(array('authToken'=>$authToken));

        if (!$user) {
            throw new InvalidAuthTokenException();
        }

        if ($user->getAuthTokenExpire() < new \DateTime()) {
            throw new AuthTokenExpiredException();
        }

        return $user;
    }

    /**
     * @param string $refreshToken
     *
     * @throws InvalidRefreshTokenException
     * @throws RefreshTokenExpiredException
     * @return User
     */
    public function findUserByRefreshToken($refreshToken)
    {
        $user = $this->findUserBy(array('refreshToken'=>$refreshToken));

        if (!$user) {
            throw new InvalidRefreshTokenException();
        }

        if ($user->getRefreshTokenExpire() < new \DateTime()) {
            throw new RefreshTokenExpiredException();
        }

        return $user;
    }

    /**
     * @param User $user
     */
    public function refreshAllTokens(User $user)
    {
        $user->setLastLogin(new \DateTime());
        $user->setAuthToken($this->tokenGenerator->generateToken());
        $user->setAuthTokenExpire(new \DateTime('+1 day'));
        $user->setRefreshToken($this->tokenGenerator->generateToken());
        $user->setRefreshTokenExpire(new \DateTime('+1 month'));
        $this->flush($user);
    }

    /**
     * @param User $user
     */
    public function refreshConfirmationToken(User $user)
    {
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function setRandomDefaultAvatar(User $user)
    {
        if ($user->getAvatar() === null) {
            $user->setAvatar('avatar/default'. rand(0, 2) .'.jpg');
        }

        return $user;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    public function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * flush
     */
    public function flush()
    {
        $this->objectManager->flush();
    }
}