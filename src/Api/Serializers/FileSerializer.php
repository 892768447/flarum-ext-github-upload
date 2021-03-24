<?php

namespace Irony\Github\Upload\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Irony\Github\Upload\File;

class FileSerializer extends AbstractSerializer
{
    protected $type = 'files';

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param File $model
     *
     * @return array
     */
    protected function getDefaultAttributes($model)
    {
        return [
            'url' => $model->url,
            'sha' => $model->sha,
            'type' => $model->type,
            'created_at' => $model->created_at,
            'name' => $model->name,
            'path' => $model->path,
        ];
    }
}