<?php declare(strict_types=1);

namespace Onviser\Mailer;

class SendToFileMailer implements MailerInterface
{
    protected string $error = '';

    public function __construct(
        protected string $directory
    )
    {
    }

    public function send(Mail $mail): bool
    {
        if (file_exists($this->directory) === false) {
            $this->error = "directory $this->directory is not exist";
            return false;
        }
        if (is_writable($this->directory) === false) {
            $this->error = "directory $this->directory is not writable";
            return false;
        }
        $filePath = $this->directory . DIRECTORY_SEPARATOR;
        $filePath .= date('Y-m-d-H-i-s') . '-';
        $filePath .= microtime(true) . '.eml';
        $file = fopen($filePath, 'w');
        if ($file === false) {
            $this->error = "failed to open the file $filePath for writing";
            return false;
        }
        if (is_writable($filePath) === false) {
            $this->error = "no permission to write to the file $filePath";
            fclose($file);
            return false;
        }
        if (fwrite($file, $mail->__toString()) === false) {
            $this->error = "file $filePath write error";
            fclose($file);
            return false;
        }
        fclose($file);
        return true;
    }

    public function getError(): string
    {
        return $this->error;
    }
}