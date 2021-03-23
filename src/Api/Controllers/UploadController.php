<?php

namespace Irony\Github\Upload\Api\Controllers;

use Exception;
use Flarum\Api\Controller\AbstractListController;
use FoF\Upload\Exceptions\InvalidUploadException;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Irony\Github\Upload\Api\Serializers\FileSerializer;
use Irony\Github\Upload\Commands\Upload;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class UploadController extends AbstractListController
{

    public $serializer = FileSerializer::class;

    /**
     * @var Dispatcher
     */
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return Collection|mixed
     * @throws Exception
     */
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        // 获取上传的文件
        $files = collect(Arr::get($request->getUploadedFiles(), 'files', []));
//        print_r($files);

        /** @var Collection $collection */
        $collection = $this->bus->dispatch(
            new Upload($files, $actor)
        );

        if ($collection->isEmpty()) {
            throw new Exception('No files were uploaded');
        }

        return $collection;
    }

}
