<?php

namespace Irony\Github\Upload\Commands;

use Exception;
use Flagrow\Upload\Templates\AbstractTemplate;
use Illuminate\Support\Collection;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\Events;
use Irony\Github\Upload\File;
use Irony\Github\Upload\Repositories\FileRepository;
use Flarum\Foundation\Application;
use Flarum\Foundation\ValidationException;
use Flarum\User\AssertPermissionTrait;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Arr;
use Psr\Http\Message\UploadedFileInterface;

class UploadHandler
{
    use AssertPermissionTrait;

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
     *
     * @return Collection
     */
    public function handle(Upload $command)
    {
        $this->assertCan(
            $command->actor,
            'irony.github.upload'
        );

        $savedFiles = $command->files->map(function (UploadedFileInterface $file) use ($command) {
            try {
                $upload = $this->files->moveUploadedFileToTemp($file);
                print_r($upload);

//                print_r($upload->getClientMimeType());

//                $this->events->fire(
//                    new Events\Adapter\Identified($command->actor, $upload, $adapter)
//                );
//
//                $file = $this->files->createFileFromUpload($upload, $command->actor);
//
//                $this->events->fire(
//                    new Events\File\WillBeUploaded($command->actor, $file, $upload)
//                );
//
//                $response = $adapter->upload(
//                    $file,
//                    $upload,
//                    $this->files->readUpload($upload, $adapter)
//                );
//
//                $this->files->removeFromTemp($upload);
//
//                if (!($response instanceof File)) {
//                    return false;
//                }
//
//                $file = $response;
//
//                $file->upload_method = $adapter;
//                $file->tag = $template;
//                $file->actor_id = $command->actor->id;
//
//                $this->events->fire(
//                    new Events\File\WillBeSaved($command->actor, $file, $upload)
//                );
//
//                if ($file->isDirty() || !$file->exists) {
//                    $file->save();
//                }
//
//                $this->events->fire(
//                    new Events\File\WasSaved($command->actor, $file, $upload)
//                );
            } catch (Exception $e) {
                if (isset($upload)) {
                    $this->files->removeFromTemp($upload);
                }

                throw $e;
            }

            return $file->base_name;
        });

        print_r($savedFiles);

        return $savedFiles->filter();
    }

    /**
     * @param $adapter
     *
     * @return UploadAdapter|null
     */
    protected function getAdapter($adapter)
    {
        if (!$adapter) {
            return;
        }

        return app("irony.github.upload-adapter.$adapter");
    }
}
