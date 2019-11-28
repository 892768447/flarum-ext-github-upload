<?php

namespace Irony\Github\Upload\Listeners;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;

class AddUploadsApi
{
    /**
     * @param Serializing $event
     */
    public function handle(Serializing $event)
    {
        if ($event->isSerializer(ForumSerializer::class)) {
            // 添加可上传属性
            $event->attributes['canUploadToGithub'] = $event->actor->can('irony.github.upload');
        }
    }
}
