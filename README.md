# Laravel Notifications for sipgate

[![Latest Version on Packagist](https://img.shields.io/packagist/v/simonkub/laravel-sipgate-notifications.svg?style=flat-square)](https://packagist.org/packages/simonkub/laravel-sipgate-notifications)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/simonkub/laravel-sipgate-notifications/master.svg?style=flat-square)](https://travis-ci.org/simonkub/laravel-sipgate-notifications)
[![StyleCI](https://styleci.io/repos/210414919/shield)](https://styleci.io/repos/210414919)
[![Quality Score](https://img.shields.io/scrutinizer/g/simonkub/laravel-sipgate-notifications.svg?style=flat-square)](https://scrutinizer-ci.com/g/simonkub/laravel-sipgate-notifications)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/simonkub/laravel-sipgate-notifications/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/simonkub/laravel-sipgate-notifications/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/simonkub/laravel-sipgate-notifications.svg?style=flat-square)](https://packagist.org/packages/simonkub/laravel-sipgate-notifications)

This package makes it easy to send notifications using [sipgate](https://sipgate.de) with Laravel 5.5+ and 6.0.

## Contents

- [Installation](#installation)
	- [Setting up the sipgate service](#setting-up-the-sipgate-service)
	- [Web SMS Extensions / SMS ID](#web-sms-extensions--sms-id)
	- [Send SMS with custom sender number](#send-sms-with-custom-sender-number)
- [Usage](#usage)
	- [Create a Notification](#create-a-notification)
	- [Add a recipient](#add-a-recipient)
	- [Sending On-Demand Notifications](#sending-on-demand-notifications)
	- [Available Message methods](#available-message-methods)
- [Common Issues](#common-issues)
    - [SMS sent successfully but no message received](#sms-sent-successfully-but-no-message-received)
    - [HTTP Errors](#http-errors)
- [Resources](#resources)
- [Changelog](#changelog)
- [Testing](#testing)
- [Security](#security)
- [Contributing](#contributing)
- [Credits](#credits)
- [License](#license)


## Installation

Install the package via composer:

```bash
composer require simonkub/laravel-sipgate-notifications
```

### Setting up the sipgate service

Extend `config/services.php` to read your sipgate credentials from your `.env`:

```php
return [
   
    ...

    'sipgate' => [
        'username' => env('SIPGATE_USERNAME'),
        'password' => env('SIPGATE_PASSWORD'),
        'smsId' => env('SIPGATE_SMSID'),
        'enabled' => env('SIPGATE_NOTIFICATOINS_ENABLED', true),
    ]
];
```

Add your sipgate credentials to your `.env`:
```bash
SIPGATE_NOTIFICATOINS_ENABLED=true
SIPGATE_USERNAME=mail@example.com
SIPGATE_PASSWORD=1234567890
SIPGATE_SMSID=s0
```

#### Web SMS Extensions / SMS ID

A Web SMS extension consists of the letter 's' followed by a number (e.g. `s0`). The sipgate API uses the concept of Web SMS extensions to identify devices within your account that are enabled to send SMS. In this context the term 'device' does not necessarily refer to a hardware phone but rather a virtual connection.

You can find out what your extension is as follows:

1. Log into your [sipgate account](https://app.sipgate.com/connections/sms)
2. Use the sidebar to navigate to the **Connections** (_Anschlüsse_) tab
3. Click **SMS** (if this option is not displayed you might need to book the **Web SMS** feature from the Feature Store)
4. The URL of the page should have the form `https://app.sipgate.com/{...}/connections/sms/{smsId}` where `{smsId}` is your Web SMS extension.

#### Send SMS with custom sender number

By default 'sipgate' will be used as the sender. It is only possible to change the sender to a mobile phone number by verifying ownership of said number. In order to accomplish this, proceed as follows:

1. Log into your [sipgate account](https://app.sipgate.com/connections/sms)
2. Use the sidebar to navigate to the **Connections** (_Anschlüsse_) tab
3. Click **SMS** (if this option is not displayed you might need to book the **Web SMS** feature from the Feature Store)
4. Click the gear icon on the right side of the **Caller ID** box and enter the desired sender number.
5. Proceed to follow the instructions on the website to verify the number.


## Usage

### Create a Notification

When your credentials are configured, you can use the `sipgate` channel in your notifications.

```php
class ExampleNotification extends Notification
{
    public function via($notifiable)
    {
        return ['sipgate'];
    }

    public function toSipgate($notifiable)
    {
        return SipgateMessage::create('Your message goes here…');
    }
}
```

### Add a recipient

You can either choose to add the recipients number to the message itself:

```php
public function toSipgate($notifiable)
{
    return SipgateMessage::create('Your message goes here…')->recipient('00491234567890');
}
```

Or add a `routeNotificationForSipgate` method to your notifiable class:

```php
class User extends Authenticatable
{
    use Notifiable;

    public function routeNotificationForSipgate($notification)
    {
        return $this->phone_number;
    }
}
```

> If you define both, the message will be send to the number you defined in the message.

### Sending On-Demand Notifications

If you want to send a notification to someone who is not registered in your application, use on-demand notifications:

```php
Notification::route('sipgate', '00491234567890')
            ->notify(new ExampleNotification($message));
```

### Available Message methods
```php
public function toSipgate($notifiable)
{
    return (new SipgateMessage('Your message goes here…'))
        ->message('…or here')
        ->recipient('00491234567890')
        ->sendAt(time() + 60)
        ->smsId('s0');
}
```

> **Optional:**
> In order to send a delayed message set the desired date and time in the future (up to one month):
>
> ```php
> $message->sendAt(time() + 60);
> ```
> 
> **Note:** The `sendAt` method accepts a [Unix timestamp](https://www.unixtimestamp.com/).


## Common Issues

#### SMS sent successfully but no message received

Possible reasons are:

- incorrect or mistyped phone number
- recipient phone is not connected to network
- long message text - delivery can take a little longer

#### HTTP Errors

| reason                                                                                                                                              | errorcode |
| --------------------------------------------------------------------------------------------------------------------------------------------------- | :-------: |
| bad request (e.g. request body fields are empty or only contain spaces, timestamp is invalid etc.)                                                  |    400    |
| username and/or password are wrong                                                                                                                  |    401    |
| insufficient account balance                                                                                                                        |    402    |
| no permission to use specified SMS extension (e.g. SMS feature not booked, user password must be reset in [web app](https://app.sipgate.com/login)) |    403    |
| internal server error or unhandled bad request (e.g. `smsId` not set)                                                                               |    500    |

## Resources

- [sipgate team FAQ (DE)](https://teamhelp.sipgate.de/hc/de)
- [sipgate basic FAQ (DE)](https://basicsupport.sipgate.de/hc/de)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email mail@simonkubiak.de instead of using the issue tracker.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Simon Kubiak](https://github.com/simonkub)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
