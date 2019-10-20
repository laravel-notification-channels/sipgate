<?php

namespace Simonkub\Laravel\Notifications\Sipgate;

use GuzzleHttp\ClientInterface as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Simonkub\Laravel\Notifications\Sipgate\Exceptions\CouldNotSendNotification;

class SipgateClient
{
    /**
     * @var HttpClient
     */
    protected $http;

    /**
     * SipgateClient constructor.
     * @param  HttpClient  $http
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * @param  SipgateMessage  $message
     * @throws CouldNotSendNotification
     */
    public function send(SipgateMessage $message)
    {
        try {
            $this->http->post('sessions/sms', ['json' => $message->toArray()]);
        } catch (GuzzleException $exception) {
            throw CouldNotSendNotification::connectionFailed($exception);
        }
    }
}
