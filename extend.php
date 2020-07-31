<?php

namespace Irony\Github\Upload;

use Flarum\Extend;
use Flarum\Api\Event\Serializing;
use Illuminate\Contracts\Events\Dispatcher;
use s9e\TextFormatter\Configurator;

return [
    (new Extend\Routes('api'))
        ->post('/irony/github/upload', 'irony.github.upload', Api\Controllers\UploadController::class),

    (new Extend\Frontend('admin'))
        ->js(__DIR__ . '/js/dist/admin.js'),

    (new Extend\Frontend('forum'))
        ->css(__DIR__ . '/resources/less/forum/forum.less')
        ->js(__DIR__ . '/js/dist/forum.js'),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    function (Dispatcher $events) {
        $events->listen(Serializing::class, Listeners\AddUploadsApi::class);
    },
    (new Extend\Formatter)
        ->configure(function (Configurator $config) {
            $config->BBCodes->addCustom(
                '[GITHUB-VIDEO]{URL1}[/GITHUB-VIDEO]',
                '<video class="githubVideo" controls><source src="{URL1}" type="video/mp4"></video>'
            );
        })
];
