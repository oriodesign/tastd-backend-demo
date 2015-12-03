<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Tastd\Bundle\CoreBundle\Entity\Device;
use Tastd\Bundle\CoreBundle\Key\Permission;
use Tastd\Bundle\CoreBundle\Repository\DeviceRepository;

/**
 * Class DeviceController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 * @Route(service="tastd.device_controller")
 */
class DeviceController extends BaseServiceController
{
    /** @var DeviceRepository */
    protected $deviceRepository;

    /**
     * @param DeviceRepository $deviceRepository
     */
    public function __construct(DeviceRepository $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Get User Device",
     *  statusCodes={200="User Device",404="User Device Not Found"},
     *  section="Device")
     * @Route("/api/devices/{id}")
     * @Method({"GET"})
     * @return View
     */
    public function getAction($id)
    {
        $device = $this->deviceRepository->get($id);
        $this->securityCheck(Permission::READ, $device);

        return $this->view(array('device' => $device));
    }

    /**
     * @ApiDoc(
     *  description="Get Devices",
     *  statusCodes={201="Devices"},
     *  section="Device",
     *  parameters={
     *      {"name"="user", "dataType"="int", "required"=false}
     *  })
     * @Route("/api/devices")
     * @Method({"GET"})
     * @return View
     */
    public function getAllAction()
    {
        $request = $this->requestStack->getCurrentRequest();
        $devices = $this->deviceRepository->getAll($request);
        foreach ($devices as $device) {
            $this->securityCheck(Permission::READ, $device);
        }

        return $this->view(array('devices' => $devices));
    }

    /**
     * @ApiDoc(
     *  description="Create Device",
     *  statusCodes={201="Device Created"},
     *  section="Device",
     *  parameters={
     *      {"name"="token", "dataType"="string", "required"=false},
     *      {"name"="user", "dataType"="int", "required"=false},
     *      {"name"="name", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/devices")
     * @Method({"POST"})
     * @return View
     */
    public function newAction()
    {
        $device = $this->deserializeCreateRequest(Device::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $device);
        $this->validate($device);
        $persistedDevice = $this->deviceRepository->getDeviceByToken($device->getToken());

        if ($persistedDevice) {
            $persistedDevice->setUser($device->getUser());
            $this->entityManager->flush();

            return $this->view(array('device' => $persistedDevice), 201);
        }

        $this->entityManager->persist($device);
        $this->entityManager->flush();

        return $this->view(array('device' => $device), 201);
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Patch Device",
     *  statusCodes={200="Device Updated",404="User Not Found"},
     *  section="Device",
     *  parameters={
     *      {"name"="id", "dataType"="integer", "required"=true},
     *      {"name"="token", "dataType"="string", "required"=false},
     *      {"name"="name", "dataType"="string", "required"=false}
     *  })
     * @Route("/api/devices/{id}")
     * @Method({"PUT"})
     * @return View
     */
    public function putAction($id)
    {
        $device = $this->deserializeUpdateRequest(Device::CLASS_NAME);
        $this->securityCheck(Permission::WRITE, $device);
        $this->validate($device);
        $this->entityManager->flush();

        return $this->view(array('device' => $device));
    }

    /**
     * @param integer $id
     *
     * @ApiDoc(
     *  description="Delete Device",
     *  statusCodes={204="Device Deleted"},
     *  section="Device")
     * @Route("/api/devices/{id}")
     * @Method({"DELETE"})
     * @return View
     */
    public function deleteAction($id)
    {
        $device = $this->deviceRepository->get($id);
        $this->securityCheck(Permission::WRITE, $device);
        $this->entityManager->remove($device);
        $this->entityManager->flush();

        return $this->view(array(), 204);
    }


}