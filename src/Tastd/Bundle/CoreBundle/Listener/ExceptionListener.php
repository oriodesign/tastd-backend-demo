<?php

namespace Tastd\Bundle\CoreBundle\Listener;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Symfony\Component\HttpKernel\EventListener\ExceptionListener as BaseExceptionListener;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\Translation\Translator;
use Tastd\Bundle\CoreBundle\Exception\Api\ApiExceptionInterface;

/**
 * Class ExceptionListener
 *
 * @package Tastd\Bundle\CoreBundle\Listener
 */
class ExceptionListener extends BaseExceptionListener
{
    protected $controller;
    protected $logger;
    protected $translator;

    public function __construct($controller, LoggerInterface $logger = null, Translator $translator)
    {
        $this->controller = $controller;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     *
     * @return bool
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        static $handling;

        if (true === $handling) {
         return false;
        }

        $handling = true;
        $exception = $event->getException();

        if ($exception instanceof ApiExceptionInterface) {
            $exception->setMessage($this->translator->trans($exception->getMessage()));
        }

        if ($exception instanceof ApiExceptionInterface && is_array($exception->getErrors())) {
            $errors = array();
            foreach ($exception->getErrors() as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $message) {
                        $errors[$key][] = $this->translator->trans($message);
                    }
                } else {
                    $errors[$key][] = $this->translator->trans($value);
                }
            }
            $exception->setErrors($errors);
        }

        /**
         * Listen only for ApiExceptions
         */
        if (!$exception instanceof ApiExceptionInterface) {
            return;
        }

        $request = $event->getRequest();

        $this->logException($exception, sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine()));

        $request = $this->duplicateRequest($exception, $request);

        try {
         $response = $event->getKernel()->handle($request, HttpKernelInterface::SUB_REQUEST, true);
        } catch (\Exception $e) {
         $this->logException($exception, sprintf('Exception thrown when handling an exception (%s: %s)', get_class($e), $e->getMessage()), false);

         // set handling to false otherwise it wont be able to handle further more
         $handling = false;

         // re-throw the exception from within HttpKernel as this is a catch-all
         return;
        }

        $event->setResponse($response);

        $handling = false;
    }

    /**
     * Clones the request for the exception.
     *
     * @param \Exception $exception
     * @param Request    $request
     *
     * @return Request $request The cloned request.
     */
    protected function duplicateRequest(\Exception $exception, Request $request)
    {
        $attributes = array(
            '_controller' => $this->controller,
            /**
             * no flatten exception because ApiExceptions are simple objects
             * and i need all parameters to be serialized
             */
            'exception' => $exception,
            'logger' => $this->logger instanceof DebugLoggerInterface ? $this->logger : null,
            'format' => $request->getRequestFormat(),
        );
        $request = $request->duplicate(null, null, $attributes);
        $request->setMethod('GET');

        return $request;
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $exception The original \Exception instance
     * @param string     $message   The error message to log
     * @param bool       $original  False when the handling of the exception thrown another exception
     */
    protected function logException(\Exception $exception, $message, $original = true)
    {
        $isCritical = $exception->getStatusCode() >= 500;
        $context = array('exception' => $exception);
        if (null !== $this->logger) {
            if ($isCritical) {
                $this->logger->critical($message, $context);
            } else {
                $this->logger->error($message, $context);
            }
        } elseif (!$original || $isCritical) {
            error_log($message);
        }
    }
}