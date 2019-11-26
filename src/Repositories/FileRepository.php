<?php

namespace Irony\Github\Upload\Repositories;

use Exception;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\File;
use Milo\Github;
use Flarum\Foundation\Application;
use Flarum\User\User;
use Flarum\Settings\SettingsRepositoryInterface;
use Illuminate\Support\Str;
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

    protected $github;

    public function __construct(Application $app, SettingsRepositoryInterface $settings)
    {
        $this->path = $app->storagePath();
        $this->settings = $settings;
        $github = new Github\Api;
        $github->setToken($settings->get('irony.github.upload.token'));
    }

    /**
     * 通过md5找到已存在的文件记录
     * @param $md5
     *
     * @return File
     */
    public function findByMd5($md5)
    {
        return File::query()
            ->where('md5', $md5)
            ->first();
    }

    /**
     * @param Upload $file
     * @param User $actor
     *
     * @return File
     */
    public function createFileFromUpload(Upload $file, User $actor)
    {
        // 上传文件到Github
        $user = $this->settings->get('irony.github.upload.user');
        // 随机取出一个项目
        $project = array_rand(explode(',', $this->settings->get('irony.github.upload.projects')));
        $url = 'https://api.github.com/repos/' . $user . '/' . $project . '/contents/' . date('Y-m-d') . '/' . $file->md5 . $file->getExtension();
        $data = [
            'message' => 'user ' . $actor->id . ' upload file: ' . $file->getClientOriginalName(),
            'content' => base64_encode(file_get_contents($file->getFilename()))
        ];
        $response = $this->github->decode($this->github->put($url, $data, [], ['Content-Type' => 'application/json']));
        return (new File())->forceFill([
            'md5' => $file->md5,
            'actor_id' => $actor->id,
        ]);
    }

    /**
     * 获取已经存在的文件
     * @param Upload $file
     * @return File|Upload|null
     */
    public function getExistsFile(Upload $file)
    {
        // 比对文件md5值
        $md5 = md5_file($file->getPathname());
        $file = $this->findByMd5($md5);
        if ($file)
            return $file;
        return $md5;
    }

    /**
     * 移动上传的文件到临时目录并判断类型和大小
     * @param UploadedFileInterface $upload
     *
     * @return Upload
     * @throws Exception
     */
    public function moveUploadedFileToTemp(UploadedFileInterface $upload)
    {
        // 检查上传错误
        $this->handleUploadError($upload->getError());

        // Move the file to a temporary location first.
        // 移动到临时文件
        $tempFile = tempnam($this->path . '/tmp', 'irony');
        $upload->moveTo($tempFile);

        // 构造新实例
        $file = new Upload(
            $tempFile,
            $upload->getClientFilename(),
            $upload->getClientMediaType(),
            $upload->getSize(),
            $upload->getError(),
            true
        );

        // 判断文件是否超过上传大小限制
        $size = $this->settings->get('maxsize', FileRepository::DEFAULT_MAX_FILE_SIZE);
        if (($file->getSize() / 1024) > $size) {
            // 删除临时文件
            unlink($tempFile);
            $this->handleUploadError(UPLOAD_ERR_FORM_SIZE);
        }

        return $file;
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

    /**
     * Deletes a file from the temporary file location.
     *
     * @param Upload $file
     *
     * @return bool
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function removeFromTemp(Upload $file)
    {
        return $this->getTempFilesystem($file->getPath())->delete($file->getBasename());
    }

    /**
     * Retrieves a filesystem manager for the temporary file location.
     *
     * @param string $path
     *
     * @return Filesystem
     */
    protected function getTempFilesystem($path)
    {
        return new Filesystem(new Local($path));
    }

    /**
     * @param Upload $upload
     * @param string $uuid
     *
     * @return string
     */
    protected function getBasename(Upload $upload, $uuid)
    {
        $name = pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME);

        $slug = trim(Str::slug($name));

        return sprintf('%s.%s',
            empty($slug) ? $uuid : $slug,
            $upload->guessExtension() ?: $upload->getClientOriginalExtension()
        );
    }

    /**
     * @param Upload $upload
     * @param UploadAdapter $adapter
     *
     * @return bool|false|resource|string
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function readUpload(Upload $upload, UploadAdapter $adapter)
    {
        $filesystem = $this->getTempFilesystem($upload->getPath());

        return $adapter->supportsStreams()
            ? $filesystem->readStream($upload->getBasename())
            : $filesystem->read($upload->getBasename());
    }
}
