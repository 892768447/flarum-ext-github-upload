<?php

namespace Irony\Github\Upload\Api\Controllers;

use Exception;
use Irony\Github\Upload\Commands\Upload;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class UploadController implements RequestHandlerInterface
{
    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $actor = $request->getAttribute('actor');
        // 获取上传的文件
        $files = collect(Arr::get($request->getUploadedFiles(), 'files', []));

        /** @var Collection $collection */
        $collection = $this->dispatcher->dispatch(
            new Upload($files, $actor)
        );

        if ($collection->isEmpty()) {
            throw new Exception('No files were uploaded');
        }

        return new JsonResponse($collection->toArray(), 201);
    }
}
