<?php

namespace HongXunPan\Validator\Message;

interface RuleMessageSourceInterface
{
    /**
     * @return MessageTemplate|null
     */
    public static function resolve($name);
}
