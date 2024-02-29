<?php declare(strict_types=1);

namespace Onviser\Mailer;

class Attachment
{
    /**
     * @param string $data
     * @param string $fileName
     * @param string $fileType
     */
    public function __construct(
        protected string $data = '',
        protected string $fileName = '',
        protected string $fileType = ''
    )
    {
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setData(string $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileType
     * @return $this
     */
    public function setFileType(string $fileType): self
    {
        $this->fileType = $fileType;
        return $this;
    }

    public function getFileType(): string
    {
        return $this->fileType;
    }
}