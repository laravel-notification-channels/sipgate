<?php

namespace Simonkub\Laravel\Notifications\Sipgate;

use Illuminate\Contracts\Support\Arrayable;

class SipgateMessage implements Arrayable
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $recipient;

    /** @var string */
    protected $smsId;

    /** @var int */
    protected $sendAt;

    public function __construct($message = '')
    {
        $this->message = $message;
    }

    public static function create($message = '')
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

    public function toArray()
    {
        return [
            'message' => $this->message,
            'recipient' => $this->recipient,
            'smsId' => $this->smsId,
            'sendAt' => $this->sendAt,
        ];
    }
}
