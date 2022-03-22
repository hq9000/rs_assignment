<?php


namespace Roadsurfer\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonResponseExceptionListener
{
    /**
     * @param ExceptionEvent $event
     *
     * @throws Exception
     * @noinspection PhpUnused
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof HttpException) {
            $response = new JsonResponse([], $throwable->getStatusCode());
        } else {
            $response = new JsonResponse([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}