<?php

namespace Irony\Github\Upload\Events\File;

use Irony\Github\Upload\File;
use Flarum\User\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class Event
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var File
     */
    public $file;

    /**
     * @var UploadedFile
     */
    public $uploadedFile;

    /**
     * @param User $actor
     * @param File $file
     * @param UploadedFile $uploadedFile
     */
    public function __construct(User $actor, File $file, UploadedFile $uploadedFile)
    {
        $this->actor = $actor;
        $this->file = $file;
        $this->uploadedFile = $uploadedFile;
    }
}
