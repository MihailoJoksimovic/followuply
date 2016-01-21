<?php

namespace Followuply\Mailing;

class Mailer
{
    public function queueEmail()
    {
        if (true) {//user emailed in last 24h (mysql table?)
            return;
        }

        $this->addToQueue();
    }    

    protected function addToQueue()
    {
        
    }
}