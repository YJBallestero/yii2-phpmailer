<?php
/*
 *  @author    Y.J. Ballestero <yanisballestero@hotmail.com>
 *  @copyright Copyright (c) 2020-2022. YJ Ballestero
 *  @license   https://github.com/YJBallestero/yii2-phpmailer/license
 *  @link      https://github.com/YJBallestero
 */

declare(strict_types = 1);

namespace yjballestero\phpmailer;

use yii\mail\BaseMessage;
use yii\mail\MessageInterface;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use yii\base\InvalidConfigException;
use function md5;
use function mt_rand;
use function is_numeric;
use function array_shift;

/**
 * Message.
 *
 * @property array           $to
 * @property array           $from
 * @property-write mixed     $textBody
 * @property array           $replyTo
 * @property null|string     $subject
 * @property-write mixed     $htmlBody
 * @property array           $bcc
 * @property string          $charset
 * @property array           $cc
 * @property PHPMailerMailer $mailer
 */
class PHPMailerMessage extends BaseMessage
{
    public PHPMailer $transport;

    /**
     * @inheritDoc
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        if (!$this->mailer instanceof PHPMailerMailer) {
            throw new InvalidConfigException('mailer');
        }

        if (!$this->transport instanceof PHPMailer) {
            throw new InvalidConfigException('transport');
        }
    }

    /**
     * @inheritDoc
     */
    public function getCharset(): string
    {
        return $this->transport->CharSet;
    }

    /**
     * @inheritDoc
     */
    public function setCharset($charset): self
    {
        $this->transport->CharSet = $charset;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFrom(): array|string
    {
        return static::formatAddress([
            [$this->transport->From, $this->transport->FromName],
        ]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setFrom($from): self
    {
        foreach (static::normalizeAddress($from) as $email => $name) {
            $this->transport->setFrom($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTo(): array
    {
        return static::formatAddress($this->transport->getToAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setTo($to): self
    {
        $this->transport->clearAddresses();

        foreach (static::normalizeAddress($to) as $email => $name) {
            $this->transport->addAddress($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getReplyTo(): array
    {
        return static::formatAddress($this->transport->getReplyToAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setReplyTo($replyTo): self
    {
        $this->transport->clearReplyTos();

        foreach (static::normalizeAddress($replyTo) as $email => $name) {
            $this->transport->addReplyTo($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCc(): array
    {
        return static::formatAddress($this->transport->getCcAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setCc($cc): self
    {
        $this->transport->clearCCs();

        foreach (static::normalizeAddress($cc) as $email => $name) {
            $this->transport->addCC($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getBcc(): array
    {
        return static::formatAddress($this->transport->getBccAddresses());
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setBcc($bcc): self
    {
        $this->transport->clearBCCs();

        foreach (static::normalizeAddress($bcc) as $email => $name) {
            $this->transport->addBCC($email, $name);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSubject(): ?string
    {
        return $this->transport->Subject;
    }

    /**
     * @inheritDoc
     */
    public function setSubject($subject): self
    {
        $this->transport->Subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setTextBody($text): self
    {
        if (empty($this->transport->Body)) {
            $this->transport->Body = $text;
            $this->transport->isHTML(false);
        }
        else {
            $this->transport->AltBody = $text;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setHtmlBody($html): MessageInterface|PHPMailerMessage|static
    {
        if (!empty($this->transport->Body)) {
            $this->transport->AltBody = $this->transport->Body;
        }

        $this->transport->Body = $html;
        $this->transport->isHTML();

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function attach($fileName, array $options = []): self
    {
        $this->transport->addAttachment(
            $fileName,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function attachContent($content, array $options = []): self
    {
        $this->transport->addStringAttachment(
            $content,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $this;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function embed($fileName, array $options = []): string
    {
        $cid = md5((string)mt_rand());

        $this->transport->addEmbeddedImage(
            $fileName,
            $cid,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $cid;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function embedContent($content, array $options = []): string
    {
        $cid = md5((string)mt_rand());

        $this->transport->addStringEmbeddedImage(
            $content,
            $cid,
            $options['fileName'] ?? '',
            PHPMailer::ENCODING_BASE64,
            $options['contentType'] ?? ''
        );

        return $cid;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function toString(): string
    {
        return $this->transport->createHeader() . "\n" . $this->transport->createBody();
    }

    /**
     * Normalizes an address.
     *
     * @param array|string $address формат адреса в Yii
     *
     * @return array normalized email => $name
     */
    private static function normalizeAddress(array|string $address): array
    {
        $res = [];

        foreach ($address as $key => $val) {
            if (is_numeric($key)) {
                $res[$val] = '';
            }
            else {
                $res[$key] = $val;
            }
        }

        return $res;
    }

    /**
     * Formats a PHPMailer address in Yii format.
     *
     * @param array $address массив пар [$email, $name]
     *
     * @return array address in the format Yii email => name или [email]
     */
    private static function formatAddress(array $address): array
    {
        $res = [];

        foreach ($address as $pair) {
            $email = (string)array_shift($pair);
            $name = (string)array_shift($pair);

            if (empty($name)) {
                $res[] = $email;
            }
            else {
                $res[$email] = $name;
            }
        }

        return $res;
    }
}
