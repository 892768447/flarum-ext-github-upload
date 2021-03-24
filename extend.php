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

    new Extend\Locales(__DIR__ . '/resources/locale'),

    // 添加属性
    (new Extend\ApiSerializer(ForumSerializer::class))
        ->attribute('canUploadToGithub', function (ForumSerializer $serializer) {
            return true;
        }),

    (new Extend\ApiSerializer(DiscussionSerializer::class))
        ->attribute('canUploadToGithub', function (DiscussionSerializer $serializer, $model) {
            return true;
        }),

    (new Extend\Formatter)
        ->configure(function (Configurator $config) {
            $config->BBCodes->addCustom(
                '[AUDIO]{URL1}[/AUDIO]',
                '<audio controls="controls" src="{URL1}">Your browser does not support the audio tag.</audio>'
            );
            $config->BBCodes->addCustom(
                '[VIDEO]{URL1}[/VIDEO]',
                '<video controls="controls" src="{URL1}">Your browser does not support the video tag.</video>'
            );
        })
];
