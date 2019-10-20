<?php

namespace Simonkub\Laravel\Notifications\Sipgate\Test;

use Illuminate\Contracts\Support\Arrayable;
use PHPUnit\Framework\TestCase;
use Simonkub\Laravel\Notifications\Sipgate\SipgateMessage;

class SipgateMessageTest extends TestCase
{
    const MESSAGE = 'Hello World!';
    const RECIPENT = '1234567890';
    const SEND_AT = 1445385600;
    const SMS_ID = 's0';

    /** @test */
    public function it_can_be_instantiate()
    {
        $message = SipgateMessage::create();

        $this->assertInstanceOf(SipgateMessage::class, $message);
    }

    /** @test */
    public function it_is_arrayable()
    {
        $message = SipgateMessage::create();

        $this->assertInstanceOf(Arrayable::class, $message);
    }

    /** @test */
    public function it_stores_a_message_via_create_function()
    {
        $message = SipgateMessage::create(self::MESSAGE);

        $this->assertSame(self::MESSAGE, $message->getMessage());
    }

    /** @test */
    public function it_stores_a_message_via_constructor()
    {
        $message = new SipgateMessage(self::MESSAGE);

        $this->assertSame(self::MESSAGE, $message->getMessage());
    }

    /** @test */
    public function it_stores_a_message_via_its_setter_function()
    {
        $message = (new SipgateMessage())->message(self::MESSAGE);

        $this->assertSame(self::MESSAGE, $message->getMessage());
    }

    /** @test */
    public function it_stores_a_recipient()
    {
        $message = SipgateMessage::create()->recipient(self::RECIPENT);

        $this->assertSame(self::RECIPENT, $message->getRecipient());
    }

    /** @test */
    public function it_stores_a_send_date()
    {
        $message = SipgateMessage::create()->sendAt(self::SEND_AT);

        $this->assertSame(self::SEND_AT, $message->getSendAt());
    }

    /** @test */
    public function it_stores_a_sms_id()
    {
        $message = SipgateMessage::create()->smsId(self::SMS_ID);

        $this->assertSame(self::SMS_ID, $message->getSmsId());
    }

    /** @test */
    public function it_can_be_converted_into_an_array()
    {
        $message = SipgateMessage::create()
            ->message(self::MESSAGE)
            ->recipient(self::RECIPENT)
            ->sendAt(self::SEND_AT)
            ->smsId(self::SMS_ID);

        $this->assertEquals([
            'message' => self::MESSAGE,
            'recipient' => self::RECIPENT,
            'sendAt' => self::SEND_AT,
            'smsId' => self::SMS_ID,
        ], $message->toArray());
    }
}
