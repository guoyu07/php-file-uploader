<?php

namespace FileUploader;

class UploadedFile
{
    /**
     * 文件上传错误码
     * 
     * @see http://php.net/manual/zh/features.file-upload.errors.php
     * @var array
     */
    private static $errors = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];

    /**
     * @var string
     */
    private $clientFilename;

    /**
     * @var string
     */
    private $clientMediaType;

    /**
     * @var int
     */
    private $size;

    /**
     * @var null|string
     */
    private $file;

    /**
     * @var bool
     */
    private $moved = false;

    /**
     * @var StreamInterface|null
     */
    private $stream;

    public function __construct(
        $streamOrFile,
        $size,
        $errorStatus,
        $clientFilename = null,
        $clientMediaType = null
    ) {
        $this->setError($errorStatus);
        $this->setSize($size);
        $this->setClientFilename($clientFilename);
        $this->setClientMediaType($clientMediaType);

        if ($this->isOk()) {
            $this->setStreamOrFile($streamOrFile);
        }
    }

    
    public function moveTo($targetPath)
    {
        $this->validActive();

        if (false === $this->isStringNotEmpty($targetPath)) {
            throw new \RuntimeException(
                'Invalid path provided for move operation; must be a non-empty string'
            );            
        }

        if ($this->file) {
            $this->moved = php_sapi_name() == 'cli'
                            ? rename($this->file, $targetPath)
                            : move_uploaded_file($this->file, $targetPath);
        } else {

        }

        if (false === $this->moved) {
            throw new \RuntimeException(
                sprintf('Uploaded file could not be moved to %s', $targetPath)
            );            
        }
    }

    private function setStreamOrFile($streamOrFile)
    {
        if (is_string($streamOrFile)) {
            $this->file = $streamOrFile;
        } else {
            throw new \InvalidArgumentException(
                'Invalid stream or file provided for UploadedFile'
            );
        }
    }

    public function getStream()
    {

    }

    public function getSize()
    {
        return $this->size;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getClientFilename()
    {
        return $this->clientFilename;
    }

    public function getClientMediaType()
    {
        return $this->clientMediaType;
    }

    public function isMoved()
    {
        return $this->moved;
    }

    private function isOk()
    {
        return $this->error === UPLOAD_ERR_OK;
    }

    private function isStringOrNull($param)
    {
        return in_array(gettype($param), ['string', 'NULL']);
    }

    private function isStringNotEmpty($param)
    {
        return is_string($param) && false === empty($param);
    }

    private function validActive()
    {
        if (false === $this->isOk()) {
            throw new \RuntimeException(
                'Cannot retrieve stream due to upload error'
            );            
        }

        if ($this->isMoved()) {
            throw new \RuntimeException(
                'Cannot retrieve stream after it has already been moved'
            );            
        }
    }

    private function setSize($size)
    {
        if (false === is_int($size)) {
            throw new \InvalidArgumentException(
                'Upload file size must be an integer'
            );
        }

        $this->size = $size;
    }

    private function setError($error)
    {
        if (false === is_int($error)) {
            throw new \InvalidArgumentException(
                'Upload file error status must be an integer'
            );            
        }

        if (false === in_array($error, UploadedFile::$errors)) {
            throw new \InvalidArgumentException(
                'Invalid error status for UploadedFile'
            );       
        }

        $this->error = $error;
    }

    private function setClientFilename($clientFilename)
    {
        if (false === $this->isStringOrNull($clientFilename)) {
            throw new \InvalidArgumentException(
                'Upload file client filename must be a string or null'
            );
            
        }

        $this->clientFilename = $clientFilename;
    }

    private function setClientMediaType($clientMediaType)
    {
        if (false === $this->isStringOrNull($clientMediaType)) {
            throw new \InvalidArgumentException(
                'Upload file client media type must be a string or null'
            );
        }

        $this->clientMediaType = $clientMediaType;
    }
}