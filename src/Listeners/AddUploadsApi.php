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
            $event->attributes['canUpload'] = $event->actor->can('irony.github.upload');
        }
    }
}
