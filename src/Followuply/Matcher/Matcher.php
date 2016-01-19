<?php

namespace Followuply\Matcher;

use Followuply\Model\Event;

class Matcher
{
    public function processEvent(Event $event)
    {
        $this->logToPersistentStorage($event);

        if (!$this->routeMatches) {
            // discard this
            return;
        }

        
    }

    protected function logToPersistentStorage(Event $event)
    {

    }

    protected function routeMatches()
    {

    }
}