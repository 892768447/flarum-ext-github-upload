<?php

namespace Irony\Github\Upload\Listeners;

use Irony\Github\Upload\Events\File\WillBeUploaded;
use Irony\Github\Upload\Processors\ImageProcessor;

class ProcessesImages
{
    /**
     * @param WillBeUploaded $event
     */
    public function handle(WillBeUploaded $event)
    {
        if ($this->validateMime($event->file->type)) {
            app(ImageProcessor::class)->process($event->file, $event->uploadedFile);
        }
    }

    /**
     * @param $mime
     * @return bool
     */
    protected function validateMime($mime)
    {
        if ($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/svg+xml') {
            return true;
        }
        return false;
    }
}
