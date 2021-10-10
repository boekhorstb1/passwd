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

class Passwd_ReacInitMin_Controller implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    protected Horde_Variables $vars;
    protected Passwd_Factory_Driver $pwFactoryDriver;
    protected Horde_Registry $registry;
    protected $conf;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Horde_Variables $vars,
        Passwd_Factory_Driver $pwFactoryDriver,
        Horde_Registry $registry
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->vars = $vars;
        $this->backends = $pwFactoryDriver->backends;
        $this->registry = $registry;       
        $this->conf = $GLOBALS['conf'];
    }

    /**
     * Handle a request
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        
        // Fallback response
        // $code = 404;
        // $reason = 'No Response by middleware or payload Handler';
        // $body = $this->streamFactory->createStream($reason);

        // return $this->responseFactory->createResponse($code, $reason)->withBody($body);
        // \Horde::debug("test1", '/dev/shm/debugpasswd.log');
        

        $userid = $request->getAttribute('HORDE_AUTHENTICATED_USER');

        if ($this->conf['user']['change'] === true) {
            $userid = $this->vars->get('userid', $this->userid);
        } else {
            try {
                $userid = Horde::callHook('default_username', [], 'passwd');
            } catch (Horde_Exception_HookNotSet $e) {}
        }

        // Get the backend details.
        $backend_key = $this->vars->backend;
        if (!isset($this->backends[$backend_key])) {
            $backend_key = null;
        }

        // Choose the prefered backend from config/backends.php.
        foreach ($this->backends as $k => $v) {
            if (!isset($backend_key) && (substr($k, 0, 1) != '_')) {
                $backend_key = $k;
            }
            if ($this->isPreferredBackend($v)) {
                $backend_key = $k;
                break;
            }
        }
        $jsGlobals = [
            'url' => $this->vars->return_to,
            'userid' => $userid,
            'userChange' => $this->conf['user']['change'],
            'showlist' => ($this->conf['backend']['backend_list'] == 'shown'),
            'backend' => $backend_key,
        ];

        $showlist = false;
        if ($showlist) {
            $jsGlobals['backends'] = $this->backends; 
            $jsGlobals['header'] = _("Change your password");
        } else {
            $jsGlobals['header'] = sprintf(_("Changing password for %s"), htmlspecialchars($this->backends[$backend_key]['name']));
        }
        
        // $page_output->addInlineJsVars([
            //     'var passwdHordeVars' => $jsGlobals
            // ]);
            
        $fileroot = $this->registry->get('fileroot', 'passwd');
        $bodyStr = file_get_contents($fileroot . '/frontend/build/index.html');
        $body = $this->streamFactory->createStream($bodyStr);
        return $this->responseFactory->createResponse(200)->withBody($body);
    }

    /**
     * Determines if the given backend is the "preferred" backend for this web
     * server.
     *
     * This decision is based on the global 'SERVER_NAME' and 'HTTP_HOST'
     * server variables and the contents of the 'preferred' field in the
     * backend's definition.  The 'preferred' field may take a single value or
     * an array of multiple values.
     *
     * @param array $backend  A complete backend entry from the $backends
     *                        hash.
     *
     * @return boolean  True if this entry is "preferred".
     */
    private function isPreferredBackend($backend)
    {
        if (!empty($backend['preferred'])) {
            if (is_array($backend['preferred'])) {
                foreach ($backend['preferred'] as $backend) {
                    if ($backend == $_SERVER['SERVER_NAME'] ||
                        $backend == $_SERVER['HTTP_HOST']) {
                        return true;
                    }
                }
            } elseif ($backend['preferred'] == $_SERVER['SERVER_NAME'] ||
                      $backend['preferred'] == $_SERVER['HTTP_HOST']) {
                return true;
            }
        }

        return false;
    }

}
