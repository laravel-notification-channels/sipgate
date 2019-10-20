<?php

namespace Simonkub\Laravel\Notifications\Sipgate\Test;

use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Mockery;
use PHPUnit\Framework\TestCase;
use Simonkub\Laravel\Notifications\Sipgate\Exceptions\CouldNotSendNotification;
use Simonkub\Laravel\Notifications\Sipgate\SipgateChannel;
use Simonkub\Laravel\Notifications\Sipgate\SipgateClient;
use Simonkub\Laravel\Notifications\Sipgate\SipgateMessage;

class SipgateChannelTest extends TestCase
{
    /**
     * @var SipgateClient|Mockery\MockInterface
     */
    protected $sipgateClient;

    /**
     * @var SipgateChannel
     */
    protected $sipgateChannel;

    /**
     * @var Mockery\MockInterface|Notification
     */
    protected $testNotification;

    protected function setUp(): void
    {
        $this->sipgateClient = Mockery::mock(SipgateClient::class);

        $this->sipgateChannel = new SipgateChannel($this->sipgateClient, 'SMS_ID_SET_IN_CONFIG', true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    /** @test */
    public function it_sends_a_notification()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_converts_a_notification_to_a_sipgate_message()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->with(Mockery::type(SipgateMessage::class))
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_prefers_the_message_recipient_over_the_notifiable_route()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->with(Mockery::on(function (SipgateMessage $argument) {
                return $argument->getRecipient() === 'RECIPIENT_SET_IN_MESSAGE';
            }))
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotificationWithRecipient());
    }

    /** @test */
    public function it_defaults_to_the_notifiable_route_when_message_has_no_recipient()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->with(Mockery::on(function (SipgateMessage $argument) {
                return $argument->getRecipient() === 'RECIPIENT_SET_IN_NOTIFIABLE';
            }))
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_throws_an_exception_when_no_recipient_is_found()
    {
        $this->expectException(CouldNotSendNotification::class);
        $this->expectExceptionMessage(CouldNotSendNotification::NO_RECIPIENT);

        $this->sipgateChannel->send(new TestNotifiableWithoutRoute(), new TestNotification());
    }

    /** @test */
    public function it_prefers_the_message_sms_id_over_the_configured_one()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->with(Mockery::on(function (SipgateMessage $argument) {
                return $argument->getSmsId() === 'SMS_ID_SET_IN_MESSAGE';
            }))
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotificationWithSmsId());
    }

    /** @test */
    public function it_defaults_to_the_configured_sms_id_when_message_has_no_sms_id()
    {
        $this->sipgateClient
            ->shouldReceive('send')
            ->with(Mockery::on(function (SipgateMessage $argument) {
                return $argument->getSmsId() === 'SMS_ID_SET_IN_CONFIG';
            }))
            ->once();

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_sends_no_messages_when_channel_is_disabled()
    {
        $this->sipgateChannel = new SipgateChannel($this->sipgateClient, 'SMS_ID_SET_IN_CONFIG', false);

        $this->sipgateClient->shouldNotReceive('send');

        $this->sipgateChannel->send(new TestNotifiable(), new TestNotification());
    }
}

class TestNotifiable
{
    use Notifiable;

    public function routeNotificationForSipgate()
    {
        return 'RECIPIENT_SET_IN_NOTIFIABLE';
    }
}

class TestNotifiableWithoutRoute
{
    use Notifiable;
}

class TestNotification extends Notification
{
    public function toSipgate($notifiable)
    {
        return new SipgateMessage('Hello World!');
    }
}

class TestNotificationWithRecipient extends Notification
{
    public function toSipgate($notifiable)
    {
        return (new SipgateMessage('Hello World!'))->recipient('RECIPIENT_SET_IN_MESSAGE');
    }
}

class TestNotificationWithSmsId extends Notification
{
    public function toSipgate($notifiable)
    {
        return (new SipgateMessage('Hello World!'))->smsId('SMS_ID_SET_IN_MESSAGE');
    }
}
