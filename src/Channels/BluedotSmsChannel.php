<?php

namespace Tjmugova\BluedotSms\Channels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use Tjmugova\BluedotSms\BluedotSms;
use Tjmugova\BluedotSms\Events\BluedotSmsSent;
use Tjmugova\BluedotSms\Exceptions\CouldNotSendNotification;
use Tjmugova\BluedotSms\Messages\BluedotSmsMessage;

class BluedotSmsChannel
{
    /**
     * The Bluedot client instance.
     *
     * @var BluedotSms
     */
    protected $client;
    protected $from;
    /**
     * @var Dispatcher
     */
    protected $events;

    public function __construct(BluedotSms $client, $from, Dispatcher $events)
    {
        $this->client = $client;
        $this->from = $from;
        $this->events = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return \Vonage\Message\Message
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $this->getTo($notifiable, $notification);
        $recipients = is_array($to) ? $to : [$to];
        foreach ($recipients as $recipient) {
            $message = $notification->toBluedotSms($notifiable);

            if (is_string($message)) {
                $message = new BluedotSmsMessage($message);
            }
            $payload = [
                'type' => $message->type,
                'from' => $message->from ?: $this->from,
                'to' => $recipient,
                'text' => trim($message->content),
            ];
            try {
                $response = $this->client->sendMessage($payload);
                if ($response['status'] === 'F') {
                    throw CouldNotSendNotification::messageRejected($response['remarks']);
                }
                event(new BluedotSmsSent($response));
            } catch (\Exception $exception) {
                $event = new NotificationFailed(
                    $notifiable,
                    $notification,
                    'bluedotSms',
                    ['message' => $exception->getMessage(), 'exception' => $exception]
                );

                $this->events->dispatch($event);
            }

        }

    }

    /**
     * Get the address to send a notification to.
     *
     * @param mixed $notifiable
     * @param Notification|null $notification
     *
     * @return mixed
     * @throws CouldNotSendNotification
     */
    protected function getTo($notifiable, $notification = null)
    {
        if ($notifiable->routeNotificationFor(self::class, $notification)) {
            return $notifiable->routeNotificationFor(self::class, $notification);
        }
        if ($notifiable->routeNotificationFor('bluedotSms', $notification)) {
            return $notifiable->routeNotificationFor('bluedotSms', $notification);
        }
        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }
        if (isset($notifiable->mobile)) {
            return $notifiable->mobile;
        }

        throw CouldNotSendNotification::invalidReceiver();
    }
}