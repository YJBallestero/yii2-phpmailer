<?php
/*
 *  @author    Y.J. Ballestero <yanisballestero@hotmail.com>
 *  @copyright Copyright (c) 2020-2022. YJ Ballestero
 *  @license   https://github.com/YJBallestero/yii2-phpmailer/license
 *  @link      https://github.com/YJBallestero
 */

declare(strict_types = 1);

namespace yjballestero\tests;

use Yii;
use PHPUnit\Framework\TestCase;
use PHPMailer\PHPMailer\PHPMailer;
use yjballestero\phpmailer\PHPMailerMailer;
use yjballestero\phpmailer\PHPMailerMessage;

/**
 * Class ScssConverterTest
 */
class PHPMailerTest extends TestCase
{
	/**
	 * Тест
	 */
	public function testSend(): void
	{
		/** @var PHPMailerMailer $mailer */
		$mailer = Yii::$app->mailer;
		self::assertInstanceOf(PHPMailerMailer::class, $mailer);

		/** @var PHPMailerMessage $message */
		$message = $mailer->compose()
		                  ->setTo(TO)
		                  ->setSubject(SUBJ)
		                  ->setTextBody('Ok');

		self::assertInstanceOf(PHPMailerMessage::class, $message);
		self::assertInstanceOf(PHPMailer::class, $message->transport);
		self::assertSame(CHARSET, $message->transport->CharSet);
		self::assertSame(FROM, $message->from);
		self::assertSame(TO, $message->to);
		self::assertSame(SUBJ, $message->subject);

		$res = $message->send();

		self::assertTrue($res, $message->transport->ErrorInfo);
	}
}
