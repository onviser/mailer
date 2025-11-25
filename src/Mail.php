<?php declare(strict_types=1);

namespace Onviser\Mailer;

class Mail
{
    public const string TYPE_TEXT = 'text/plain';
    public const string TYPE_HTML = 'text/html';

    public const string CHARSET_UTF_8 = 'utf-8';

    protected string $type = self::TYPE_TEXT;
    protected string $charset = self::CHARSET_UTF_8;
    protected string $subject = '';
    protected string $body = '';
    protected string $from = '';
    protected string $fromName = '';
    protected string $replayTo = '';

    /**
     * Time for the "Date" header.
     * @var int
     */
    protected int $time = 0;

    /**
     * Separator for attachments in the email body.
     * @var string
     */
    protected string $boundary = 'boundary';

    /** @var string[] */
    protected array $to = [];

    /** @var string[] */
    protected array $copy = [];

    /** @var string[] */
    protected array $blindCopy = [];

    /** @var Attachment[] */
    protected array $attachments = [];

    public function __construct(
        string $subject = '',
        string $body = '',
        string $from = '',
        string $to = ''
    )
    {
        $this->setSubject($subject);
        $this->setBody($body);
        $this->setFrom($from);
        if ($to !== '') {
            $this->to($to);
        }
    }

    public function setSubject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function to(string $to): static
    {
        if ($to !== '') {
            $this->to[] = trim($to);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getTo(): array
    {
        return $this->to;
    }

    public function copy(string $copy): static
    {
        $this->copy[] = trim($copy);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getCopy(): array
    {
        return $this->copy;
    }

    public function blindCopy(string $blindCopy): static
    {
        $this->blindCopy[] = trim($blindCopy);
        return $this;
    }

    /**
     * @return string[]
     */
    public function getBlindCopy(): array
    {
        return $this->blindCopy;
    }

    public function setFrom(string $from, string $fromName = ''): static
    {
        $this->from = $from;
        $this->fromName = $fromName;
        return $this;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function setReplayTo(string $replayTo): static
    {
        $this->replayTo = $replayTo;
        return $this;
    }

    public function getReplayTo(): string
    {
        return $this->replayTo;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setCharset(string $charset): static
    {
        $this->charset = $charset;
        return $this;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;
        return $this;
    }

    public function getTime(): int
    {
        return $this->time;
    }

    public function attachment(Attachment $attachment): static
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    /**
     * @return Attachment[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function __toString(): string
    {
        $EOL = "\r\n";

        $data = 'MIME-Version: 1.0' . $EOL;
        if (count($this->attachments) > 0) {
            $data .= 'Content-Type: multipart/mixed; boundary="' . $this->boundary . '"' . $EOL;
        } else {
            $data .= 'Content-Type: ' . $this->type . '; charset=' . $this->charset . $EOL;
            $data .= 'Content-Transfer-Encoding: 8bit' . $EOL;
        }
        $data .= 'From: ' . ($this->getFromName() === ''
                ? $this->getFrom()
                : $this->encode($this->getFromName()) . ' <' . $this->getFrom() . '>') . $EOL;
        if ($this->getReplayTo() !== '') {
            $data .= 'Reply-To: ' . $this->getReplayTo() . $EOL;
        }
        $data .= 'Subject: ' . $this->encode($this->getSubject()) . $EOL;
        $data .= 'To: ' . implode(', ', $this->to) . $EOL;
        if (count($this->copy) > 0) {
            $data .= 'Cc: ' . implode(', ', $this->copy) . $EOL;
        }
        $data .= 'Date: ' . date('D, d M Y H:i:s', $this->time > 0 ? $this->time : time()) . $EOL;
        $data .= $EOL;

        if (count($this->attachments) > 0) {
            $data .= '--' . $this->boundary . $EOL;
            $data .= 'Content-Type: ' . $this->type . '; charset=' . $this->charset . $EOL;
            $data .= 'Content-Transfer-Encoding: 8bit' . $EOL;
            $data .= $EOL;
            $data .= $this->getBody() . $EOL;
            $data .= $EOL;
            foreach ($this->attachments as $attachment) {
                $data .= '--' . $this->boundary . $EOL;
                $data .= 'Content-Type: ' . $attachment->getFileType() . '; name="' . $attachment->getFileName() . '"' . $EOL;
                $data .= 'Content-transfer-encoding: base64' . $EOL;
                $data .= 'Content-Disposition: attachment; filename="' . $attachment->getFileName() . '"' . $EOL;
                $data .= $EOL;
                $data .= base64_encode($attachment->getData()) . $EOL;
            }
            $data .= '--' . $this->boundary . '--' . $EOL;
        } else {
            $data .= $this->getBody() . $EOL;
        }
        $data .= $EOL;

        return $data;
    }

    protected function encode(string $value): string
    {
        return '=?' . $this->charset . '?B?' . base64_encode($value) . '?=';
    }
}