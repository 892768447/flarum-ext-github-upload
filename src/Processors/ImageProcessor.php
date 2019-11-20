<?php

namespace Irony\Github\Upload\Processors;

use Irony\Github\Upload\Contracts\Processable;
use Irony\Github\Upload\File;
use Irony\Github\Upload\Helpers\Settings;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageProcessor implements Processable
{
    const DEFAULT_MAX_IMAGE_WIDTH = 960;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param File         $file
     * @param UploadedFile $upload
     *
     * @return File
     */
    public function process(File &$file, UploadedFile &$upload)
    {
        $mimeType = $upload->getClientMimeType();
        if ($mimeType == 'image/jpeg' || $mimeType == 'image/png') {
            $image = (new ImageManager())->make($upload->getRealPath());

            // 调整图片大小
            $this->resize($image);

            // 添加水印
            $this->watermark($image);

            @file_put_contents(
                $upload->getRealPath(),
                $image->encode($upload->getClientMimeType())
            );
        }
    }

    /**
     * @param Image $manager
     */
    protected function resize(Image $manager)
    {
        $manager->resize(
            ImageProcessor::DEFAULT_MAX_IMAGE_WIDTH,
            null,
            function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
    }

    /**
     * @param Image $image
     */
    protected function watermark(Image $image)
    {
        if ($this->settings->get('irony.github.upload.watermark')) {
            // 添加网站地址
            // $image->insert(
            // );
        }
    }
}
