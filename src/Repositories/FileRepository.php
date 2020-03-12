<?php

namespace Irony\Github\Upload\Repositories;

use Exception;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\File;
use League\Flysystem\FileNotFoundException;
use Milo\Github;
use Flarum\Foundation\Application;
use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as Upload;

class FileRepository
{
    const DEFAULT_MAX_FILE_SIZE = 1024;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var SettingsRepositoryInterface
     */
    protected $settings;

    public function __construct(Application $app, SettingsRepositoryInterface $settings)
    {
        $this->path = $app->storagePath();
        $this->settings = $settings;
    }

    /**
     * 通过sha找到已存在的文件记录
     * @param $sha
     *
     * @return File
     */
    public function findBySha($sha)
    {
        return File::query()
            ->where('sha', $sha)
            ->first();
    }

    /**
     * 上传文件到Github
     * @param UploadedFileInterface $file
     * @param User $actor
     * @return File
     * @throws Exception
     */
    public function uploadToGithub(UploadedFileInterface $file, User $actor)
    {
        // 检查上传错误
        $this->handleUploadError($file->getError());
        // 移动到临时文件
        $tempFile = tempnam($this->path . '/tmp', 'irony');
        $file->moveTo($tempFile);

        // 构造新实例
        $file = new Upload($tempFile, $file->getClientFilename(),
            $file->getClientMediaType(), $file->getSize(),
            $file->getError(), true
        );

        // 判断文件是否超过上传大小限制
        $size = $this->settings->get('irony.github.upload.maxsize', FileRepository::DEFAULT_MAX_FILE_SIZE);
        if (($file->getSize() / 1024) > $size) {
            // 删除临时文件
            unlink($file->getRealPath());
            $this->handleUploadError(UPLOAD_ERR_FORM_SIZE);
        }

        // 文件后缀名
        $ext = str_replace('jpeg', 'jpg', $file->guessExtension() ?: $file->getClientOriginalExtension());
        $mime = $file->getClientMimeType();
        $originalName = $file->getClientOriginalName();
        // 获取已经存在的文件
        // 比对文件sha值
        $fileData = file_get_contents($file->getRealPath());
        // 删除临时文件
        unlink($file->getRealPath());
        $sha = sha1('blob ' . strlen($fileData) . chr(0) . $fileData);
        $existFile = $this->findBySha($sha);
        if ($existFile)
            return $existFile;

        // 图片水印

        // 上传文件到Github
        // https://developer.github.com/v3/repos/contents/#create-or-update-a-file
        $user = $this->settings->get('irony.github.upload.user');
        // 随机取出一个项目
        $projects = explode(',', $this->settings->get('irony.github.upload.projects'));
        $project = $projects[array_rand($projects)];
        $url = 'https://api.github.com/repos/' . $user . '/' . $project . '/contents/' . date('Y-m-d') . '/' . $sha . '.' . $ext;
        $data = [
            'message' => 'user ' . $actor->id . ' upload file: ' . $originalName,
            'content' => base64_encode($fileData)
        ];
        unset($fileData);

        $github = new Github\Api;
        $token = new Github\OAuth\Token($this->settings->get('irony.github.upload.token'));
        $github->setToken($token);

        $response = $github->decode($github->put($url, $data, [], ['Content-Type' => 'application/json']));
        if (!$response->content) {
            $this->handleUploadError(UPLOAD_ERR_CANT_WRITE);
        }

        $file = (new File())->forceFill([
            'actor_id' => $actor->id,
            'url' => str_replace('raw.githubusercontent.com/', 'cdn.jsdelivr.net/gh/', str_replace('/master/', '/', $response->content->download_url)),
            'sha' => $response->content->sha,
            'type' => $this->getFileType($mime)
        ]);
        // 存入数据库记录
        if (!$file->save()) {
            $this->handleUploadError(UPLOAD_ERR_CANT_WRITE);
        };
        return $file;
    }

    protected function getFileType($mime)
    {
        if ($mime == 'image/jpeg' || $mime == 'image/png' || $mime == 'image/gif' || $mime == 'image/svg+xml') {
            return 'image';
        }
        return 'file';
    }


    protected function handleUploadError($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                throw new Exception('Upload max filesize limit reached from php.ini.');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                throw new Exception('Upload max filesize limit reached from form.');
                break;
            case UPLOAD_ERR_PARTIAL:
                throw new Exception('Partial upload.');
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new Exception('No file uploaded.');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                throw new Exception('No tmp folder for uploading files.');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                throw new Exception('Cannot write to disk');
                break;
            case UPLOAD_ERR_EXTENSION:
                throw new Exception('A php extension blocked the upload.');
                break;
            case UPLOAD_ERR_OK:
                break;
        }
    }

}
