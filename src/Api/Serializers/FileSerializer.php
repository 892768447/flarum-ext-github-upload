<?php

namespace Irony\Github\Upload\Api\Serializers;

use Irony\Github\Upload\File;
use Irony\Github\Upload\Helpers\Settings;
use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Formatter\Formatter;
use Illuminate\Support\Arr;

class FileSerializer extends AbstractSerializer
{
    protected $type = 'files';
    /**
     * @var Settings
     */
    private $settings;
    /**
     * @var Formatter
     */
    private $formatter;

    public function __construct(Settings $settings, Formatter $formatter)
    {
        $this->settings = $settings;
        $this->formatter = $formatter;
    }

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param File $model
     *
     * @return array
     */
    protected function getDefaultAttributes($model)
    {
        $attributes = Arr::only(
            $model->attributesToArray(),
            ['uuid', 'base_name', 'tag']
        );

        return $attributes;
    }
}
