<?php

namespace Irony\Github\Upload\Repositories;

use Exception;
use Irony\Github\Upload\Commands\Download as DownloadCommand;
use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\Download;
use Irony\Github\Upload\File;
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

    public function __construct(Application $app, SettingsRepositoryInterface $settings)
    {
        $this->path = $app->storagePath();
        $this->settings = $settings;
    }

    /**
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
        // unique file md5
        // 比对文件md5值
        $md5 = md5_file($file->getPathname());
        $efile = $this->findByMd5($md5);
        if ($efile)
            return $efile;

        return (new File())->forceFill([
            'md5' => $md5,
            'actor_id' => $actor->id,
        ]);
    }

    /**
     * @param UploadedFileInterface $upload
     *
     * @return Upload
     */
    public function moveUploadedFileToTemp(UploadedFileInterface $upload)
    {
        // 检查上传错误
        $this->handleUploadError($upload->getError());
        // 判断文件是否超过上传大小限制
        $size = $this->settings->get('maxsize', FileRepository::DEFAULT_MAX_FILE_SIZE);
        if (($upload->getSize() / 1024) > $size)
            throw new Exception('Upload max filesize limit ' . $size);
        //$this->validator->assertValid(compact('upload'));

        // Move the file to a temporary location first.
        // 移动到临时文件
        $tempFile = tempnam($this->path . '/tmp', 'irony');
        $upload->moveTo($tempFile);

        $file = new Upload(
            $tempFile,
            $upload->getClientFilename(),
            $upload->getClientMediaType(),
            $upload->getSize(),
            $upload->getError(),
            true
        );

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
     */
    public function readUpload(Upload $upload, UploadAdapter $adapter)
    {
        $filesystem = $this->getTempFilesystem($upload->getPath());

        return $adapter->supportsStreams()
            ? $filesystem->readStream($upload->getBasename())
            : $filesystem->read($upload->getBasename());
    }

    /**
     * @param File $file
     * @param DownloadCommand $command
     *
     * @return Download
     */
    public function downloadedEntry(File $file, DownloadCommand $command)
    {
        $download = new Download();

        $download->forceFill([
            'file_id' => $file->id,
            'discussion_id' => $command->discussionId,
            'post_id' => $command->postId,
        ]);

        if ($command->actor && !$command->actor->isGuest()) {
            $download->actor_id = $command->actor->id;
        }

        $download->save();

        return $download;
    }
}
