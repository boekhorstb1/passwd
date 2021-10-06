<?php

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**

 */
class Passwd_ChangePasswordReact_Controller implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;

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
        
        $this->_vars = $GLOBALS['injector']->getInstance('Horde_Variables');
        $this->_backends = $GLOBALS['injector']->getInstance('Passwd_Factory_Driver')->backends;
        global $conf, $page_output;

        $this->_userid = $request->getAttribute('HORDE_AUTHENTICATED_USER');

        if ($conf['user']['change'] === true) {
            $this->_userid = $this->_vars->get('userid', $this->_userid);
        } else {
            try {
                $this->_userid = Horde::callHook('default_username', [], 'passwd');
            } catch (Horde_Exception_HookNotSet $e) {}
        }

        // Get the backend details.
        $backend_key = $this->_vars->backend;
        if (!isset($this->_backends[$backend_key])) {
            $backend_key = null;
        }

        // Choose the prefered backend from config/backends.php.
        foreach ($this->_backends as $k => $v) {
            if (!isset($backend_key) && (substr($k, 0, 1) != '_')) {
                $backend_key = $k;
            }
            if ($this->_isPreferredBackend($v)) {
                $backend_key = $k;
                break;
            }
        }
        $jsGlobals = [
            'url' => $this->_vars->return_to,
            'userid' => $this->_vars->_userid,
            'userChange' => $conf['user']['change'],
            'showlist' => ($conf['backend']['backend_list'] == 'shown'),
            'backend' => $backend_key,
        ];

        ob_start();
        $page_output->header(array(
            'title' => _("Change Password"),
            'view' => $GLOBALS['registry']::VIEW_BASIC
        ));
        $view = new Horde_View(array(
            'templatePath' => PASSWD_TEMPLATES
        ));
        if ($view->showlist) {
            $jsGlobals['backends'] = $this->_backends; 
            $view->header = _("Change your password");
        } else {
            $view->header = sprintf(_("Changing password for %s"), htmlspecialchars($this->_backends[$backend_key]['name']));
        }

        $page_output->sidebar = false;

        $page_output->addScriptFile('stripe.js', 'horde');
        $page_output->addScriptFile('passwd-react.js', 'passwd');

        $page_output->addInlineJsVars([
            'var passwdHordeVars' => $jsGlobals
        ]);
        
        echo $view->render('index-react');
        $page_output->footer();

        $bodyStr = ob_get_contents();
        ob_end_clean();

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
    private function _isPreferredBackend($backend)
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
