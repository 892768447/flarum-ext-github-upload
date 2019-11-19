<?php

namespace Irony\Github\Upload\Events\File;

use Irony\Github\Upload\File;

class WasLoaded
{
    /**
     * @var File
     */
    public $file;

    public function __construct(File $file)
    {
        $this->file = $file;
    }
}
