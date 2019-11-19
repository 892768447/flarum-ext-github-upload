<?php

namespace Irony\Github\Upload\Contracts;

use Irony\Github\Upload\Commands\Download;
use Irony\Github\Upload\File;
use Psr\Http\Message\ResponseInterface;

interface Downloader
{
    /**
     * Whether the upload adapter works on a specific mime type.
     *
     * @param File $file
     *
     * @return bool
     */
    public function forFile(File $file);

    /**
     * @param File     $file
     * @param Download $command
     *
     * @return ResponseInterface
     */
    public function download(File $file, Download $command);
}
