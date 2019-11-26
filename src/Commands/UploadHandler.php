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
     * @throws \Flarum\User\Exception\PermissionDeniedException
     */
    public function handle(Upload $command)
    {
        $this->assertCan(
            $command->actor,
            'irony.github.upload'
        );

        $savedFiles = $command->files->map(function (UploadedFileInterface $file) use ($command) {
            try {
//                print_r($upload->getClientMimeType());

                // 移动文件到临时目录
                $upload = $this->files->moveUploadedFileToTemp($file);
                // 获取已经存在的文件
                $fileormd5 = $this->files->getExistsFile($upload);
                if ($fileormd5 instanceof File)
                    return $fileormd5->url;
                // 记录md5
                $upload->md5 = $fileormd5;
                // 添加图片水印
                // 开始上传文件,并删除临时文件
                $upload = $this->files->createFileFromUpload($upload, $command->actor);
                // 失败 return false;
                // 存入数据库记录
                $upload->save();

            } catch (Exception $e) {
                if (isset($upload)) {
                    // 失败则删除临时文件
                    $this->files->removeFromTemp($upload);
                }

                throw $e;
            }

            return $file->base_name;
        });

        print_r($savedFiles);

        return $savedFiles->filter();
    }

}
