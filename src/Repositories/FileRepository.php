<?php

namespace Irony\Github\Upload\Repositories;

use Exception;
use Flarum\Foundation\Paths;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\User;
use Illuminate\Container\Container;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\File;
use Milo\Github;
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

//    public function __construct(Application $app, SettingsRepositoryInterface $settings)
//    {
//        $this->path = $app->storagePath();
//        $this->settings = $settings;
//    }

    public function __construct(Paths $paths, SettingsRepositoryInterface $settings)
    {
        $this->path = $paths->storage;
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

        // 移动文件到 public/assets/files 文件夹中
        $publicPath = Container::getInstance()->make(Paths::class)->public;
        $searches = [];
        $replaces = [];
        if (is_link($filesDir = $publicPath . DIRECTORY_SEPARATOR . 'assets/files')) {
            $searches[] = realpath($filesDir);
            $replaces[] = 'assets/files';
        }
        if (!is_dir($filesDir)) {
            mkdir($filesDir, 0777, true);
        }
        if (is_link($assetsDir = $publicPath . DIRECTORY_SEPARATOR . 'assets')) {
            $searches[] = realpath($assetsDir);
            $replaces[] = 'assets';
        }
        $searches = array_merge($searches, [$publicPath, DIRECTORY_SEPARATOR]);
        $replaces = array_merge($replaces, ['', '/']);

        // 移动文件
        $tempFile1 = tempnam($filesDir, 'irony');
        $tempFile = $tempFile1 . '_' . $file->getClientFilename();
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
            if (file_exists($tempFile1))
                unlink($tempFile1);
            if (file_exists($file->getRealPath()))
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
        // 文件时间
        $fileDate = date('Y-m-d H:i:s', $file->getCTime());
        // 文件 sha1
        $sha = sha1('blob ' . strlen($fileData) . chr(0) . $fileData);
        $existFile = $this->findBySha($sha);

        // 数据库中存在或者不保留则删除
        if ($existFile || strval($this->settings->get('irony.github.upload.keepfiles', 0)) !== '1') {
            // 删除临时文件
            $tempFile = null;
            if (file_exists($tempFile1))
                unlink($tempFile1);
            if (file_exists($file->getRealPath()))
                unlink($file->getRealPath());
        }

        // 数据库中已经记录则直接返回
        if ($existFile)
            return $existFile;
        if (file_exists($tempFile1))
            unlink($tempFile1);

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

        $url = $response->content->download_url;
        if (((string)$this->settings->get('irony.github.upload.jsdelivrcdn')) == '1')
            $url = str_replace('raw.githubusercontent.com/', 'cdn.jsdelivr.net/gh/',
                str_replace('/main/', '@main/', str_replace('/master/', '@master/', $url)));

        try {
            // new
            $file = (new File())->forceFill([
                'actor_id' => $actor->id,
                'name' => $originalName,
                'path' => str_replace($searches, $replaces, $tempFile),
                'url' => $url,
                'sha' => $response->content->sha,
                'type' => $this->getFileType($mime),
                'created_at' => $fileDate,
            ]);
            // 存入数据库记录
            if (!$file->save()) {
                $this->handleUploadError(UPLOAD_ERR_CANT_WRITE);
            }
        } catch (Exception $e) {
            // old
            $file = (new File())->forceFill([
                'actor_id' => $actor->id,
                'url' => $url,
                'sha' => $response->content->sha,
                'type' => $this->getFileType($mime),
            ]);
            // 存入数据库记录
            if (!$file->save()) {
                $this->handleUploadError(UPLOAD_ERR_CANT_WRITE);
            }
        }
        return $file;
    }

    protected function getFileType($mime)
    {
        if (strpos($mime, 'image/') === 0) {
            return 'image';
        }
        if (strpos($mime, 'audio/') === 0) {
            return 'audio';
        }
        if (strpos($mime, 'video/') === 0) {
            return 'video';
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
