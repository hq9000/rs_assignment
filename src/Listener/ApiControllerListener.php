<?php

namespace Roadsurfer\Listener;


use Symfony\Component\HttpKernel\Event\ControllerEvent;

class ApiControllerListener
{
    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $ct      = $request->getContentType();
        if ($ct == 'json') {
            $json = $request->getContent();
            $data = json_decode($json, true);
            $request->request->replace(is_array($data) ? $data : []);
        }
    }
}