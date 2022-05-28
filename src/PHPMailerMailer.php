<?php
/*
 *  @author    Y.J. Ballestero <yanisballestero@hotmail.com>
 *  @copyright Copyright (c) 2020-2022. YJ Ballestero
 *  @license   https://github.com/YJBallestero/yii2-phpmailer/license
 *  @link      https://github.com/YJBallestero
 */

declare(strict_types = 1);

namespace yjballestero\phpmailer;

use Yii;
use yii\base\Exception;
use yii\mail\BaseMailer;
use PHPMailer\PHPMailer\PHPMailer;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use function array_merge;

/**
 * A mail service that uses PHPMailer as transport.
 */
class PHPMailerMailer extends BaseMailer
{
    /** @inheritDoc */
    public $messageClass = PHPMailerMessage::class;
    
    /**@var $transportConfig \PHPMailer\PHPMailer\PHPMailer|array */
    public array|\PHPMailer\PHPMailer\PHPMailer $transportConfig = [];

    /**
     * Creates transport.
     *
     * @return PHPMailer
     * @throws InvalidConfigException
     */
    protected function createTransport(): PHPMailer
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Yii::createObject(array_merge([
            'class' => PHPMailer::class,
        ], $this->transportConfig ?: []));
    }

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    protected function createMessage(): PHPMailerMessage
    {
        /** @var PHPMailerMessage $msg first we create a message with the transport (before initialization) */
        $msg = Yii::createObject(array_merge([
            'class'     => $this->messageClass,
            'mailer'    => $this,
            'transport' => $this->createTransport(),
        ]));

        // we initialize the message already with the transport
        if (!empty($this->messageConfig)) {
            Yii::configure($msg, $this->messageConfig);
        }

        return $msg;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function sendMessage($message): bool
    {
        if (!$message instanceof PHPMailerMessage) {
            throw new InvalidArgumentException('Not supported message type');
        }

        try {
            return $message->transport->send();
        } catch (\PHPMailer\PHPMailer\Exception $ex) {
            throw new Exception('Send error', 0, $ex);
        }
    }
}
