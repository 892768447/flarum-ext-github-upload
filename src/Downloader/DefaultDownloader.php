<?php

namespace Irony\Github\Upload\Downloader;

use Irony\Github\Upload\Commands\Download;
use Irony\Github\Upload\Contracts\Downloader;
use Irony\Github\Upload\Exceptions\InvalidDownloadException;
use Irony\Github\Upload\File;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class DefaultDownloader implements Downloader
{
    /**
     * @var Client
     */
    private $api;

    public function __construct(Client $api)
    {
        $this->api = $api;
    }

    /**
     * Whether the upload adapter works on a specific mime type.
     *
     * @param File $file
     *
     * @return bool
     */
    public function forFile(File $file)
    {
        return true;
    }

    /**
     * @param File     $file
     * @param Download $command
     *
     * @throws InvalidDownloadException
     *
     * @return ResponseInterface
     */
    public function download(File $file, Download $command)
    {
        try {
            $response = $this->api->get($file->url);
        } catch (\Exception $e) {
            throw new InvalidDownloadException($e->getMessage());
        }

        if ($response->getStatusCode() == 200) {
            $response = $this->mutateHeaders($response, $file);

            return $response;
        }
    }

    /**
     * @param ResponseInterface $response
     * @param File              $file
     *
     * @return ResponseInterface
     */
    protected function mutateHeaders(ResponseInterface $response, File $file)
    {
        $response = $response->withHeader('Content-Type', 'application/force-download');
        $response = $response->withAddedHeader('Content-Type', 'application/octet-stream');
        $response = $response->withAddedHeader('Content-Type', 'application/download');

        $response = $response->withHeader('Content-Transfer-Encoding', 'binary');

        $response = $response->withHeader(
            'Content-Disposition',
            sprintf('attachment; filename="%s"', $file->base_name)
        );

        return $response;
    }
}
