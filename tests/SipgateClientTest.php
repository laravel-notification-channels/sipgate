<?php

namespace Simonkub\Laravel\Notifications\Sipgate\Test;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Simonkub\Laravel\Notifications\Sipgate\Exceptions\CouldNotSendNotification;
use Simonkub\Laravel\Notifications\Sipgate\SipgateClient;
use Simonkub\Laravel\Notifications\Sipgate\SipgateMessage;

class SipgateClientTest extends TestCase
{
    const MESSAGE = 'Hello World!';
    const RECIPENT = '1234567890';
    const SEND_AT = 1445385600;
    const SMS_ID = 's0';

    const SMS_ENDPOINT = 'sessions/sms';

    /**
     * @var SipgateClient
     */
    protected $sipgateClient;

    /**
     * @var ClientInterface|Mockery\MockInterface
     */
    protected $httpClient;

    /**
     * @var SipgateMessage
     */
    protected $message;

    protected function setUp(): void
    {
        $this->httpClient = Mockery::mock(ClientInterface::class);

        $this->sipgateClient = new SipgateClient($this->httpClient);

        $this->message = (new SipgateMessage())
            ->message(self::MESSAGE)
            ->recipient(self::RECIPENT)
            ->sendAt(self::SEND_AT)
            ->smsId(self::SMS_ID);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_posts_to_the_sipgate_api()
    {
        $this->httpClient
            ->shouldReceive('post')
            ->withArgs($this->getPostMethodArguments())
            ->once();

        $this->sipgateClient->send($this->message);
    }

    /**
     * @return array
     */
    protected function getPostMethodArguments(): array
    {
        return [
            self::SMS_ENDPOINT,
            ['json' => $this->message->toArray()],
        ];
    }

    /** @test */
    public function it_throws_an_exception_when_an_api_call_fails()
    {
        $this->httpClient
            ->shouldReceive('post')
            ->withArgs($this->getPostMethodArguments())
            ->once()
            ->andThrow(TransferException::class);

        $this->expectException(CouldNotSendNotification::class);

        $this->sipgateClient->send($this->message);
    }
}
