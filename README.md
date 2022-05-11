# PHPMailer adapter for Yii2

Mail service for Yii2 using as transport [PHPMailer](https://github.com/PHPMailer/PHPMailer).

Unlike the standard SwiftMailer, it supports sending using the php mail function.

## Requirements

This library uses PHP 8.0+.

## Install

It is recommended that you install the PHP Browser library [through composer](http://getcomposer.org). To do so, run the following command:

```sh
composer require yjballestero/yii2-phpmailer
```

## Setting

```php
 $config = [
     'components' => [
        'mailer' => [
            'class' => yjballestero\phpmailer\PHPMailerMailer::class,
            
            // config \PHPMailer\PHPMailer\PHPMailer
            'transportConfig' => [
                'CharSet' => CHARSET
            ],
            
            // default message config
            'messageConfig' => [
                'from' => FROM
            ]
        ]
    ]
];
```
