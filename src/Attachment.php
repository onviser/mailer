<?php declare(strict_types=1);

namespace Onviser\Mailer;

class Attachment
{
    public function __construct(
        protected string $data = '',
        protected string $fileName = '',
        protected string $fileType = ''
    )
    {
    }

    public function setData(string $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function setFileType(string $fileType): static
    {
        $this->fileType = $fileType;
        return $this;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }
}