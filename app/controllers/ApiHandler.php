<?php

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use \Horde_Variables;
use \Passwd_Factory_Driver;
use \Horde_Registry;
use \Horde_Config;

class Passwd_ApiHandler_Controller implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Horde_Registry $registry;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Horde_Registry $registry
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->registry = $registry;       
    }

    /**
     * Handle a request
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        $data = [
            "test" => "hello",
        ];
        $body = $this->streamFactory->createStream(json_encode($data));
        return $this->responseFactory->createResponse(200)
            ->withBody($body)
            ->withHeader('Accept', 'application/json');
    }

}
