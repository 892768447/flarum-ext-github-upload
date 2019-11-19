<?php

namespace Irony\Github\Upload\Contracts;

use Irony\Github\Upload\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface Processable
{
    /**
     * @param File         $file
     * @param UploadedFile $upload
     *
     * @return File
     */
    public function process(File &$file, UploadedFile &$upload);
}
