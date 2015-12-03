<?php

namespace Tastd\Bundle\CoreBundle\Controller;
use ArrayIterator;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Serializer;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tastd\Bundle\CoreBundle\Cache\CacheManager;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Event\ApiEvent;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\BadRequestException;
use Tastd\Bundle\CoreBundle\Exception\Api\Validation\ValidationException;
use Tastd\Bundle\CoreBundle\Key\SerializationGroup;
use JMS\Serializer\SerializationContext;
use Tastd\Bundle\CoreBundle\Pager\OutOfRangePager;
use Tastd\Bundle\CoreBundle\Security\Authorization\SecurityChecker;


/**
 * Class BaseServiceController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 */
abstract class BaseServiceController
{
    /** @var HttpKernel */
    protected $httpKernel;

    /** @var RequestStack */
    protected $requestStack;

    /** @var Serializer */
    protected $serializer;

    /** @var EntityManager  */
    protected $entityManager;

    /** @var ValidatorInterface  */
    protected $validator;

    /** @var EventDispatcherInterface */
    protected $dispatcher;

    /** @var SecurityContextInterface  */
    protected $securityContext;

    /** @var SecurityChecker  */
    protected $securityChecker;

    /** @var CacheManager */
    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function setCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * @param SecurityChecker $securityChecker
     */
    public function setSecurityChecker(SecurityChecker $securityChecker)
    {
        $this->securityChecker = $securityChecker;
    }

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function setValidator(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param RequestStack $requestStack
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param Serializer $serializer
     */
    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param HttpKernel $httpKernel
     */
    public function setHttpKernel(HttpKernel $httpKernel)
    {
        $this->httpKernel = $httpKernel;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param array|null     $data
     * @param integer|null   $statusCode
     * @param array          $headers
     * @param array          $serializationGroups
     *
     * @return View
     */
    protected function view(
        $data = null,
        $statusCode = null,
        array $headers = array(),
        $serializationGroups = array(SerializationGroup::BASE, SerializationGroup::MIN))
    {
        $clientGroups = $this->getClientSerializationGroups();
        $serializationGroups = array_merge($clientGroups, $serializationGroups);
        $serializationContext = SerializationContext::create()->setGroups($serializationGroups);
        $view = View::create($data, $statusCode, $headers);
        $view->setSerializationContext($serializationContext);

        return $view;
    }

    /**
     * @return array
     */
    public function getClientSerializationGroups()
    {
        $groupsMap = array(
            'isMyLeader' => SerializationGroup::IS_MY_LEADER, // @TODO REMOVE THIS
            'isMyFollower' => SerializationGroup::IS_MY_FOLLOWER, // @TODO REMOVE THIS
            'reviewOwner' => SerializationGroup::REVIEW_OWNER
        );
        $request = $this->requestStack->getCurrentRequest();
        $serializationGroupsParam = $request->query->get('serializationGroups');
        $serializationGroups = explode(',', $serializationGroupsParam);
        $results = array();
        foreach ($serializationGroups as $key) {
            if (isset($groupsMap[$key])) {
                $results[] = $groupsMap[$key];
            }
        }
        return $results;
    }

    /**
     * @param $controller
     * @param array $path
     * @param array $query
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function forward($controller, array $path = array(), array $query = array())
    {
        $path['_controller'] = $controller;
        $subRequest = $this->requestStack->getCurrentRequest()->duplicate($query, null, $path);

        return $this->httpKernel->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
    }

    /**
     * @param Pagerfanta $pager
     * @param string     $name
     *
     * @return array
     */
    protected function getPagedViewData($pager, $name)
    {
        $results = $pager->getCurrentPageResults();

        if ($results instanceof ArrayIterator) {
            $results = $results->getArrayCopy();
        }

        return array(
            $name  => $results,
            'meta' => $this->getPagerMeta($pager)
        );
    }

    /**
     * @param Pagerfanta $pager
     *
     * @return array
     */
    protected function getPagerMeta(Pagerfanta $pager)
    {
        if ($pager instanceof OutOfRangePager) {
            return array(
                'currentPage' => $pager->getOriginalPage(),
                'maxPerPage' => $pager->getMaxPerPage(),
                'hasNextPage' => false,
                'outOfRange' => true
            );
        }

        $meta = array(
            'currentPage' => $pager->getCurrentPage(),
            'maxPerPage' => $pager->getMaxPerPage(),
            'pagesCount' => $pager->getNbPages(),
            'hasNextPage' => $pager->hasNextPage(),
            'resultsCount' => $pager->getNbResults(),
            'hasPreviousPage' => $pager->hasPreviousPage()
        );

        if ($pager->hasNextPage()) {
            $meta['nextPage'] = $pager->getNextPage();
        }

        if ($pager->hasPreviousPage()) {
            $meta['previousPage'] = $pager->getPreviousPage();
        }

        return $meta;
    }


    /**
     * @param string $className
     * @param array  $groups
     * @param string $format
     *
     * @return mixed
     *
     * @throws BadRequestException
     */
    protected function deserializeRequest($className, $groups = array(SerializationGroup::EDIT), $format='json')
    {
        $context = DeserializationContext::create()->setGroups($groups);
        try {
            $object = $this->serializer
                ->deserialize($this->requestStack->getCurrentRequest()->getContent(), $className, $format, $context);
        } catch (\Exception $e) {
            $exception = new BadRequestException();
            $exception->setInfo($e->getMessage());
            throw $exception;
        }

        return $object;
    }

    /**
     * Shortcut method to deserialize create from json
     * @param string $className
     *
     * @return mixed
     */
    protected function deserializeCreateRequest($className)
    {
        $object = $this->deserializeRequest($className, array(SerializationGroup::CREATE), $format = 'json');

        return $object;
    }

    /**
     * Shortcut method to deserialize update from json
     * @param string $className
     *
     * @return mixed
     */
    protected function deserializeUpdateRequest($className)
    {
        $object = $this->deserializeRequest($className, array(SerializationGroup::EDIT), $format = 'json');

        return $object;
    }

    /**
     * @param mixed   $object
     * @param mixed   $groups
     * @param boolean $traverse
     * @param boolean $deep
     *
     * @throws ValidationException
     */
    protected function validate($object, $groups = null, $traverse = false, $deep = false)
    {
        $data = array();
        $errors = $this->validator->validate($object, $groups, $traverse, $deep);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                /** @var ConstraintViolationInterface $error */
                $data[$error->getPropertyPath()][] = $error->getMessage();
            }
            throw new ValidationException($data);
        }
    }

    /**
     * @param string   $eventName
     * @param ApiEvent $event
     */
    protected function dispatch($eventName, ApiEvent $event)
    {
        $this->dispatcher->dispatch($eventName, $event);
    }

    /**
     * Get a user from the Security Context.
     *
     * @return User|null
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->securityContext) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->securityContext->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    /**
     * @param string $role
     * @param mixed  $object
     */
    protected function securityCheck($role, $object = null)
    {
        $this->securityChecker->check($role, $object);
    }
}