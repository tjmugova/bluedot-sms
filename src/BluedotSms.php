<?php


namespace Tjmugova\BluedotSms;


use Illuminate\Http\Client\Factory;
use Tjmugova\BluedotSms\Exceptions\HttpException;
use Tjmugova\BluedotSms\Messages\BluedotSmsMessage;

class BluedotSms
{
    /**
     * The HTTP client instance.
     *
     * @var Factory
     */
    protected $http;
    /**
     * The phone number notifications should be sent from.
     *
     * @var string
     */
    protected $config;

    public function __construct(Factory $httpClient, $config)
    {
        $this->http = $httpClient;
        $this->config = $config;
    }

    /**
     * @param array $payload
     * @return mixed
     * @throws HttpException
     */
    public function sendMessage(array $payload)
    {
        try {
            $response = $this->http->get($this->config['api_url'], [
                'api_id' => $this->config['api_id'],
                'api_password' => $this->config['api_password'],
                'sender_id' => $payload['from'] ?? $this->config['sms_from'],
                'sms_type' => 'P',
                'encoding' => 'T',
                'phonenumber' => $payload['to'],
                'textmessage' => $payload['text'],
            ]);
            return $response->collect();

        } catch (\Throwable $e) {
            throw new HttpException($e->getMessage(), 4003);
        }
    }
}