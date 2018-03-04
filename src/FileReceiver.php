<?php

namespace FileUploader;

use FileUploader\UploadedFile;

class FileReceiver
{
    private $uploadedFiles = [];

    private function __construct()
    {
    }

    public static function make()
    {
        return new self();
    }

    public static function receive()
    {
        $fileReceiver = self::make();

        return $fileReceiver->withUploadedFiles(self::normalizeFiles($_FILES));
    }

    public function withUploadedFiles(array $uploadedFiles)
    {
        $new = clone $this;

        $new->uploadedFiles = $uploadedFiles;
        return $new;
    }

    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public static function normalizeFiles(array $files)
    {
        $normalized = [];

        foreach ($files as $key => $value) {
            if (is_array($value) && isset($value['tmp_name'])) {
                $normalized[$key] = self::createUploadedFileFromSpec($value);
                var_dump($normalized);
            } elseif (is_array($value)) {
                $normalized[$key] = $value;
                var_dump($normalized);
                continue;
            } else {
                throw new InvalidArgumentException(
                    'Invalid value in files specification'
                );                
            }
        }

        return $normalized;
    }

    public static function createUploadedFileFromSpec(array $value)
    {
        if (is_array($value['tmp_name'])) {
            return self::normalizedNestedFileSpec($value);
        }

        return new UploadedFile(
            $value['tmp_name'],
            (int) $value['size'],
            (int) $value['error'],
            $value['name'],
            $value['type']
        );
    }

    private static function normalizedNestedFileSpec(array $files = [])
    {
        $normalized = [];

        foreach (array_keys($files['tmp_name']) as $key) {
            $spec = [
                'tmp_name' => $files['tmp_name'][$key],
                'size' => $files['size'][$key],
                'error' => $files['error'][$key],
                'type' => $files['type'][$key],
                'name' => $files['name'][$key]
            ];

            $normalized[$key] = self::createUploadedFileFromSpec($spec);
        }

        return $normalized;
    }
}
