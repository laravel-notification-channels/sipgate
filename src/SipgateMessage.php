<?php

namespace NotificationChannels\Sipgate;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class SipgateMessage implements Arrayable, JsonSerializable
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $recipient;

    /** @var string */
    protected $smsId;

    /** @var int */
    protected $sendAt;

    /**
     * SipgateMessage constructor.
     * @param  string  $message
     */
    public function __construct(string $message = '')
    {
        $this->message = $message;
    }

    /**
     * @param  string  $message
     * @return static
     */
    public static function create(string $message = '')
    {
        return new static($message);
    }

    /**
     * @return string
     */
    public function getSmsId()
    {
        return $this->smsId;
    }

    /**
     * @param  string  $smsId
     *
     * @return SipgateMessage
     */
    public function smsId(string $smsId)
    {
        $this->smsId = $smsId;

        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param  string  $message
     * @return SipgateMessage
     */
    public function message(string $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @param  string  $recipient
     * @return SipgateMessage
     */
    public function recipient(string $recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * @return int | null
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @param  int  $sendAt
     * @return SipgateMessage
     */
    public function sendAt($sendAt)
    {
        $this->sendAt = $sendAt;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->message,
            'recipient' => $this->recipient,
            'smsId' => $this->smsId,
            'sendAt' => $this->sendAt,
        ];
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
