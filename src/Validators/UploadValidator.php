<?php

namespace Irony\Github\Upload\Validators;

use Irony\Github\Upload\Helpers\Settings;
use Flarum\Foundation\AbstractValidator;

class UploadValidator extends AbstractValidator
{
    const DEFAULT_MAX_FILE_SIZE = 1024;

    protected function getRules()
    {
        /** @var Settings $settings */
        $settings = app(Settings::class);

        return [
            'file' => [
                'required',
                'max:'.$settings->get('maxsize', UploadValidator::DEFAULT_MAX_FILE_SIZE),
            ],
        ];
    }
}
