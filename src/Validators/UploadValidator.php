<?php

namespace Irony\Github\Upload\Validators;

use Irony\Github\Upload\Helpers\Settings;
use Flarum\Foundation\AbstractValidator;

class UploadValidator extends AbstractValidator
{
    protected function getRules()
    {
        /** @var Settings $settings */
        $settings = app(Settings::class);

        return [
            'file' => [
                'required',
                'max:'.$settings->get('maxsize', Settings::DEFAULT_MAX_FILE_SIZE),
            ],
        ];
    }
}
