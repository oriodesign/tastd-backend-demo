<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use Doctrine\Common\Util\Debug;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Tastd\Bundle\CoreBundle\Entity\PushMessage;
use Tastd\Bundle\CoreBundle\Exception\Api\Http\AccessDeniedException;
use Tastd\Bundle\CoreBundle\Cache\CacheTag;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Manager\PushMessageManager;
use Tastd\Bundle\CoreBundle\Repository\PushMessageRepository;


/**
 * Class PushMessageController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.push_message_controller")
 */
class PushMessageController extends BaseServiceController
{

    protected $pushMessageRepository;
    protected $pushMessageManager;

    /**
     * @param PushMessageRepository $pushMessageRepository
     * @param PushMessageManager    $pushMessageManager
     */
    public function __construct(
        PushMessageRepository $pushMessageRepository,
        PushMessageManager $pushMessageManager)
    {
        $this->pushMessageRepository = $pushMessageRepository;
        $this->pushMessageManager = $pushMessageManager;
    }

    /**
     * @ApiDoc(
     *  description="Push Message list",
     *  statusCodes={200="Push Message"},
     *  section="Push Message",
     *  filters={
     *      {"name"="user", "dataType"="integer", "required"="false"},
     *  })
     * @Route("/api/push-messages")
     * @Method({"GET"})
     * @return View
     * @throws AccessDeniedException
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $userId = $request->query->get('user');
        if ($this->getUser()->getId() !== intVal($userId)) {
            throw new AccessDeniedException();
        }
        $pager = $this->pushMessageRepository->getAll($request);
        $this->cacheManager->tagController($request, CacheTag::PUSH_MESSAGE);

        return $this->view($this->getPagedViewData($pager, 'pushMessages'));
    }

    /**
     * @ApiDoc(
     *  description="Get badge counter",
     *  statusCodes={200="Push Message"},
     *  section="Push Message",
     *  filters={
     *      {"name"="user", "dataType"="integer", "required"="false"},
     *  })
     * @Route("/api/push-messages/counter")
     * @Method({"GET"})
     * @return View
     * @throws AccessDeniedException
     */
    public function getBadgeCounter()
    {
        $request = $this->requestStack->getCurrentRequest();
        $userId = $request->query->get('user');
        if ($this->getUser()->getId() !== intVal($userId)) {
            throw new AccessDeniedException();
        }
        $counter = $this->pushMessageRepository->getUnseenCounter($userId);
        $this->cacheManager->tagController($request, CacheTag::PUSH_MESSAGE);

        return $this->view(array('counter' => $counter));

    }

    /**
     * @ApiDoc(
     *  description="Seen",
     *  statusCodes={200="Seen"},
     *  section="Push Message",
     *  parameters={
     *      {"name"="user", "dataType"="string", "required"="true"},
     *  })
     * @Route("/api/push-messages/seen")
     * @Method({"POST"})
     * @return View
     * @throws AccessDeniedException
     */
    public function seenAction()
    {
        $user = $this->getUser();
        $this->pushMessageManager->markAllAsSeen($user->getId());
        $this->cacheManager->invalidateTags(array(CacheTag::PUSH_MESSAGE . CacheTag::USER . $user->getId()));

        return $this->view(array());
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Patch Push Message",
     *  statusCodes={200="Push Message Updated",404="User Not Found"},
     *  section="Push Message",
     *  parameters={
     *      {"name"="id", "dataType"="string", "required"=true},
     *      {"name"="seen", "dataType"="string", "required"=true}
     *  })
     * @Route("/api/push-messages/{id}")
     * @Method({"PUT"})
     * @return View
     */
    public function putAction($id)
    {
        $user = $this->getUser();
        $pushMessage = $this->deserializeUpdateRequest(PushMessage::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $pushMessage);
        $this->validate($pushMessage);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnUpdate($pushMessage);

        return $this->view(array('pushMessage' => $pushMessage));
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Delete PushMessage",
     *  statusCodes={204="PushMessage Deleted"},
     *  section="Push Message")
     * @Route("/api/push-messages/{id}")
     * @Method({"DELETE"})
     * @return View
     */
    public function deleteAction($id)
    {
        $user = $this->getUser();
        $pushMessage = $this->pushMessageRepository->get($id);
        $this->securityCheck(Permission::WRITE, $pushMessage);
        $this->entityManager->remove($pushMessage);
        $this->entityManager->flush();
        $this->cacheManager->invalidateOnDelete($pushMessage);

        return $this->view(array(), 204);
    }
}