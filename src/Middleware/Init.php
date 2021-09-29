<?php
declare(strict_types=1);

namespace Horde\Passwd\Middleware;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use \Horde_Registry;
use \Horde_Application;
use Horde_Controller;
use Horde_Routes_Mapper as Router;
use \Horde_String;
use \Horde;
use \Passwd_Basic as PwBasic;

/**
 * AuthHordeSession middleware
 *
 * Purpose: Identify the session as either user or a guest
 * 
 * 
 * 
 * 
 */

class InitApp implements MiddlewareInterface
{
    private Horde_Registry $registry;
    public function __construct(Horde_Registry $registry)
    {
        $this->registry = $registry;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        require_once __DIR__ . '/lib/Application.php';
        Horde_Registry::appInit('passwd');

        $ob = new PwBasic($injector->getInstance('Horde_Variables'));

            return $handler->handle($request);
        }
}
