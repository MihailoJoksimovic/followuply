<?php

namespace Followuply\Model;

use Symfony\Component\HttpFoundation\Request;

class Event
{
    const REDIS_KEY = 'events';

    public $visitorUid;

    public $timestamp;

    public $appId;

    public $uri;

    private function __construct() {}

    public static function fromRequest(Request $request)
    {
        $event = new self();

        $event->visitorUid = $request->request->get('visitor_uid');
        $event->appId = $request->request->get('app_id');
        $event->uri = $request->request->get('uri');

        return $event;
    }

    public static function fromJsonString($jsonString)
    {
        $eventData = json_decode($jsonString, true);
        $event = new self();

        $event->visitorUid = $eventData->visitorUid;
        $event->appId = $eventData->appId;
        $event->uri = $eventData->uri;

        return $event;
    }
}