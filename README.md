# PHPMailer adapter for Yii2

Mail service for Yii2 using as transport [PHPMailer](https://github.com/PHPMailer/PHPMailer).

Unlike the standard SwiftMailer, it supports sending using the php mail function.

## Requirements

This library uses:

* PHP 8.0+.
* Yii2 2.0.39+

## Install

It is recommended that you install the PHP Browser library [through composer](http://getcomposer.org). To do so, run the following command:

```sh
composer require yjballestero/yii2-phpmailer
```

Or add this line into your `composer.json` file:

```json
"yjballestero/yii2-phpmailer": "dev-master"
```

## Setting

```php
 $config = [
     'components' => [
        'mailer' => [
            'class' => yjballestero\phpmailer\PHPMailerMailer::class,            
            // config \PHPMailer\PHPMailer\PHPMailer
            'transportConfig' => [
                'Mailer'     => 'smtp', //Send using SMTP
                'CharSet'    => CHARSET, //us-ascii, iso-8859-1, utf-8
                'Encoding'   => ENCODING, //7bit, 8bit, base64, binary, quoted-printable
                'Host'       => 'smtp.example.com', //Set the SMTP server to send through
                'Username'   => 'user@example.com', //SMTP username
                'Password'   => 'secret', //SMTP password
                'Port'       => MAIL_PORT, //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                'SMTPSecure' => SMTP_ENCRYPT, //TLS, SSL
                'SMTPAuth'   => true, //Enable SMTP authentication
            ],
            
            // default message config
            'messageConfig' => [
                'from' => FROM
            ]
        ]
    ]
];
```

## A Simple Example of Use

```php
public function sendEmail() {
    $to = 'test@example.com';
    $title = 'test';
    $subject = 'test email';
    $message = 'Hello world';
    
    $email = Yii::$app->mailer->compose(['content'=>$message, 'title'=>$title])
                              ->setTo($to)
                              ->setSubject($subject);
    if($email->send()){
        return 'Message has been sent';
    }
    return $email->mailer->adapter->ErrorInfo;
}
```
