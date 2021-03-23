<?php

namespace Irony\Github\Upload\Commands;

use Exception;
use Flarum\Foundation\Application;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\Events;
use Irony\Github\Upload\Repositories\FileRepository;
use Psr\Http\Message\UploadedFileInterface;

class UploadHandler
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Dispatcher
     */
    protected $events;
    /**
     * @var FileRepository
     */
    protected $files;


    public function __construct(Application $app, Dispatcher $events, FileRepository $files)
    {
        $this->app = $app;
        $this->events = $events;
        $this->files = $files;
    }

    /**
     * @param Upload $command
     * @return Collection
     */
    public function handle(Upload $command)
    {

        $savedFiles = $command->files->map(function (UploadedFileInterface $file) use ($command) {
            try {
                $file = $this->files->uploadToGithub($file, $command->actor);
//                if ($upload->type == 'image') {
//                    return '[IMG]' . $upload->url . '[/IMG]';
//                }
//                if ($upload->type == 'video') {
//                    return '[GITHUB-VIDEO]' . $upload->url . '[/GITHUB-VIDEO]';
//                }
//                return '[' . $upload->url . '](' . $upload->url . ')';
            } catch (Exception $e) {
                //throw $e;
            }
            return $file;
        });

        return $savedFiles->filter();
    }

}
