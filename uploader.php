<?php

include('src/UploadedFile.php');
include('src/FileReceiver.php');

use FileUploader\FileReceiver;

$fileReceiver = FileReceiver::receive();
$files = $fileReceiver->getUploadedFiles();

var_dump($fileReceiver);
var_dump($files);

if (is_object($files['fileToUpload']) && $file = $files['fileToUpload']){
    $file->moveTo('F:\php_workspace\php-file-uploader\uploaded\\' . $file->getClientFilename());
} else {
    foreach ($files['fileToUpload'] as $file) {
        $file->moveTo('F:\php_workspace\php-file-uploader\uploaded\\' . $file->getClientFilename());
    }
}