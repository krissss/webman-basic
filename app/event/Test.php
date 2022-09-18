<?php

namespace app\event;

class Test
{
    public function test($data, $eventName)
    {
        dump($data, $eventName);
    }
}
