<?php
/*
 *  @author    Y.J. Ballestero <yanisballestero@hotmail.com>
 *  @copyright Copyright (c) 2020-2022. YJ Ballestero
 *  @license   https://github.com/YJBallestero/yii2-phpmailer/license
 *  @link      https://github.com/YJBallestero
 */

/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types = 1);

/**  */
define('YII_DEBUG', true);
/** */
define('YII_ENV', 'dev');

require_once(__DIR__ . '/../vendor/autoload.php');
require_once(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

/** @var string */
const CHARSET = 'UTF-8';

/** @var string[] */
const FROM = ['test@yjballestero.org' => 'PHPUnit'];

/** @var string[] */
const TO = ['develop@yjballestero.org' => 'YJ Ballestero'];

/** @var string */
const SUBJ = 'Yii2 PHPMailer Test';

// Appendix
new yii\web\Application([
	'id'         => 'test-app',
	'basePath'   => __DIR__,
	'components' => [
		'cache' => yii\caching\FileCache::class,

		'request' => [
			'scriptFile' => __FILE__,
			'scriptUrl'  => '/',
		],

		'mailer' => [
			'class'           => yjballestero\phpmailer\PHPMailerMailer::class,
			'transportConfig' => [
				'CharSet' => CHARSET,
			],
			'messageConfig'   => [
				'from' => FROM,
			],
		],
	],
]);
