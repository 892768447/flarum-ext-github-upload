<?php
namespace Irony\Github\Upload\Adapters;

use Irony\Github\Upload\Contracts\UploadAdapter;
use Irony\Github\Upload\File;
use Irony\Github\Upload\Helpers\Settings;
use Flarum\Foundation\ValidationException;

/**
 *
 */
class Qiniu extends Flysystem implements UploadAdapter
{

    /**
     * @param File $file
     */
    protected function generateUrl(File $file)
    {
        /** @var Settings $settings */
        $settings = app()->make(Settings::class);
        $path     = $file->getAttribute('path');
        if ($cdnUrl = $settings->get('cdnUrl')) {
            $file->url = sprintf('%s/%s', $cdnUrl, $path);
        } else {
            throw new ValidationException(['upload' => 'QiNiu cloud CDN address is not configured.']);

        }
    }
}
