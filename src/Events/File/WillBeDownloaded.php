<?php

namespace Irony\Github\Upload\Events\File;

use Irony\Github\Upload\Download;
use Irony\Github\Upload\File;

class WillBeDownloaded
{
    /**
     * @var File
     */
    public $file;
    public $response;
    /**
     * @var Download
     */
    public $download;

    public function __construct(File $file, &$response, Download $download = null)
    {
        $this->file = $file;
        $this->response = $response;
        $this->download = $download;
    }
}
