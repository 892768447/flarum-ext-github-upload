<?php

namespace Irony\Github\Upload;

use Flarum\Extend;
use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Api\Serializer\DiscussionSerializer;
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

    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('canUploadToGithub', function (ForumSerializer $serializer) {
            return true;
        }),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('canUploadToGithub', function (DiscussionSerializer $serializer, $model) {
            return true;
        }),

    new Extend\Locales(__DIR__ . '/resources/locale'),

    (new Extend\Event())
        ->listen(Serializing::class, Listeners\AddUploadsApi::class),

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
