<?php

namespace Tastd\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\ExceptionController as BaseExceptionController;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpKernel\Exception\FlattenException as HttpFlattenException;
use Symfony\Component\Debug\Exception\FlattenException as DebugFlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use Tastd\Bundle\CoreBundle\Exception\Api\ApiExceptionInterface;



/**
 * Class ExceptionController
 *
 * @package Tastd\Bundle\CoreBundle\Controller
 */
class ExceptionController extends BaseExceptionController
{

    /**
     * Converts an Exception to a Response.
     *
     * @param Request                                    $request
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param DebugLoggerInterface                       $logger
     * @param string                                     $format
     *
     * @return Response
     *
     * @throws \InvalidArgumentException
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        /**
         * Add support for ApiExceptionInterface
         */
        if (!$exception instanceof DebugFlattenException &&
            !$exception instanceof HttpFlattenException &&
            !$exception instanceof ApiExceptionInterface) {
            throw new \InvalidArgumentException(sprintf(
                'ExceptionController::showAction can only accept some exceptions (%s, %s), "%s" given',
                'Symfony\Component\HttpKernel\Exception\FlattenException',
                'Symfony\Component\Debug\Exception\FlattenException',
                get_class($exception)
            ));
        }

        $format = $this->getFormat($request, $format);
        if (null === $format) {
            $message = 'No matching accepted Response format could be determined, while handling: ';
            $message .= $this->getExceptionMessage($exception);

            return new Response($message, Codes::HTTP_NOT_ACCEPTABLE, $exception->getHeaders());
        }

        $currentContent = $this->getAndCleanOutputBuffering();
        $code = $this->getStatusCode($exception);
        $viewHandler = $this->container->get('fos_rest.view_handler');
        $parameters = $this->getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);

        try {
            if (!$viewHandler->isFormatTemplating($format)) {
                $parameters = $this->createExceptionWrapper($parameters);
            }

            $view = View::create($parameters, $code, $exception->getHeaders());
            $view->setFormat($format);

            if ($viewHandler->isFormatTemplating($format)) {
                $view->setTemplate($this->findTemplate($request, $format, $code, $this->container->get('kernel')->isDebug()));
            }

            $response = $viewHandler->handle($view);
        } catch (\Exception $e) {
            $message = 'An Exception was thrown while handling: ';
            $message .= $this->getExceptionMessage($exception);
            $response = new Response($message, Codes::HTTP_INTERNAL_SERVER_ERROR, $exception->getHeaders());
        }

        return $response;
    }



    /**
     * Determines the parameters to pass to the view layer.
     *
     * Overwrite it in a custom ExceptionController class to add additionally parameters
     * that should be passed to the view layer.
     *
     * @param ViewHandler                                $viewHandler
     * @param string                                     $currentContent
     * @param int                                        $code
     * @param HttpFlattenException|DebugFlattenException $exception
     * @param DebugLoggerInterface                       $logger
     * @param string                                     $format
     *
     * @return array
     */
    protected function getParameters(ViewHandler $viewHandler, $currentContent, $code, $exception, DebugLoggerInterface $logger = null, $format = 'html')
    {
        $parameters  = array(
            'status' => 'error',
            'status_code' => $code,
            'status_text' => array_key_exists($code, Response::$statusTexts) ? Response::$statusTexts[$code] : "error",
            'currentContent' => $currentContent,
            'message' => $this->getExceptionMessage($exception),
            'exception' => $exception,
        );

        /*
         * Extra info for ApiExceptions
         */
        if ($exception instanceof ApiExceptionInterface) {
            $parameters['id'] = $exception->getId();
            $parameters['info'] = $exception->getInfo();
            $parameters['category'] = $exception->getCategory();
            $parameters['errors'] = $exception->getErrors();
        }

        if ($viewHandler->isFormatTemplating($format)) {
            $parameters['logger'] = $logger;
        }

        return $parameters;
    }
}