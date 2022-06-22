<?php

declare(strict_types=1);

namespace Tjmugova\BluedotSms\Exceptions;

class InvalidConfigException extends \Exception
{
    public static function missingConfig(): self
    {
        return new self('Missing config. You must set either the api id & password or sender id');
    }
}
