<?php

declare(strict_types=1);

namespace Tjmugova\BluedotSms\Exceptions;


use Tjmugova\BluedotSms\Messages\BluedotSmsMessage;

class CouldNotSendNotification extends \Exception
{
    public static function invalidMessageObject($message): self
    {
        $className = is_object($message) ? get_class($message) : 'Unknown';

        return new static(
            "Notification was not sent. Message object class `{$className}` is invalid. It should
            be  `" . BluedotSmsMessage::class . '`');
    }

    public static function messageRejected($message): self
    {


        return new static(
            "Notification was not sent. Error message `{$message}`");
    }

    public static function missingFrom(): self
    {
        return new static('Notification was not sent. Missing `sender` id.');
    }

    public static function invalidReceiver(): self
    {
        return new static(
            'The notifiable did not have a receiving phone number. Add a routeNotificationForBluedotSms
            method or a phone_number attribute to your notifiable.'
        );
    }

    public static function missingAlphaNumericSender(): self
    {
        return new static(
            'Notification was not sent. Missing `alphanumeric_sender` in config'
        );
    }
}
