<?php

namespace Tastd\Bundle\CoreBundle\Listener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;


/**
 * Class EnableCacheListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class EnableCacheListener
{
    protected $cacheEnabled;
    protected $request;

    /**
     * @param $cacheEnabled
     */
    public function __construct($cacheEnabled, RequestStack $requestStack)
    {
        $this->cacheEnabled = $cacheEnabled;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $response = $event->getResponse();
        $serializationGroupsParameter = $this->request->get('serializationGroups');
        $serializationGroups = is_string($serializationGroupsParameter) ?
            explode(',',$serializationGroupsParameter) : array();


        if (in_array('isMyLeader', $serializationGroups) ||
            in_array('isMyFollower', $serializationGroups) ||
            $this->cacheEnabled === false) {
            $response->headers->set('Cache-Control', 'no-cache, private');
        }
    }

}