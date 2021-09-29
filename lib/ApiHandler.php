<?php

use \Horde_Controller_Request as Request;
use \Horde_Controller_Response as Response;

/**
 * Implements the REST endpoint for the customer entity
 */
class Insysgui_ApiHandler_Controller extends \Horde_Controller_Base
{
    protected $registry;

    // Copypaste - For now we cannot inherit from RestController
    protected $routeVars;
    protected $mapper;

    public function __construct(\Horde_Registry $registry)
    {
        $this->registry = $registry;
        $injector = $GLOBALS['injector'];
    }
    /**
     * Process the incoming request
     *
     * @param Request  $request  Request Object for the view
     * @param Response $response Response Object for the view
     *
     * @return void
     */
    public function processRequest(Request $request, Response $response)
    {
        $this->response = $response;
        $injector = $this->getInjector();
        // TODO: Maybe we should not inject here but use the constructor?
        $this->mapper = $injector->getInstance('Horde_Routes_Mapper');
        $this->routeVars = $this->mapper->match($request->getPath());
        $action = $this->routeVars['action'] ?? "";
        $response->setHeader('Content-Type', 'application/json');

        if(is_callable(array($this, $action))){
            $this->$action($request, $response);
        } else{
            $this->invalidResponse(); //or some kind of error message
        }
    }

    /**
     * Default response for invalid requests
     */
    public function invalidResponse(string $errorCode = "404", string $errorTxt = "Not Found")
    {
        $this->response->setHeader('Content-Type', 'text/html');
        $this->response->setHeader("HTTP/1.0 $errorCode ", $errorTxt);
        $this->response->setBody("<html><body><h1>$errorCode</h1><p>$errorTxt</p></body></html>");
    }

    /**
     * Retrieve customer data
     *
     * Get rest/customer/:customerUid - Get data on a specific customer
     * Get rest/customer/ - List customers the requester has access to TBD
     *
     * Get Parameters:
     *   - format  optional  [full|head|...] defaults to head
     */
    public function changePw(Request $request, Response $response)
    {
        global $conf, $injector, $notification, $registry;

        // Check for users that cannot change their passwords.
        if (in_array($this->_userid, $conf['user']['refused'])) {
            $notification->push(sprintf(_("You can't change password for user %s"), $userid), 'horde.error');
            return;
        }

        // We must be passed the old (current) password.
        if (!isset($this->_vars->oldpassword)) {
            $notification->push(_("You must give your current password"), 'horde.warning');
            return;
        }

        if (!isset($this->_vars->newpassword0)) {
            $notification->push(_("You must give your new password"), 'horde.warning');
            return;
        }
        if (!isset($this->_vars->newpassword1)) {
            $notification->push(_("You must verify your new password"), 'horde.warning');
            return;
        }

        if ($this->_vars->newpassword0 != $this->_vars->newpassword1) {
            $notification->push(_("Your new passwords didn't match"), 'horde.warning');
            return;
        }

        if ($this->_vars->newpassword0 == $this->_vars->oldpassword) {
            $notification->push(_("Your new password must be different from your current password"), 'horde.warning');
            return;
        }

        $b_ptr = $this->_backends[$backend_key];

        try {
            Horde_Auth::checkPasswordPolicy($this->_vars->newpassword0, isset($b_ptr['policy']) ? $b_ptr['policy'] : array());
        } catch (Horde_Auth_Exception $e) {
            $notification->push($e, 'horde.warning');
            return;
        }

        // Do some simple strength tests, if enabled in the config file.
        if (!empty($conf['password']['strengthtests'])) {
            try {
                Horde_Auth::checkPasswordSimilarity($this->_vars->newpassword0, array($this->_userid, $this->_vars->oldpassword));
            } catch (Horde_Auth_Exception $e) {
                $notification->push($e, 'horde.warning');
                return;
            }
        }

        try {
            $driver = $injector->getInstance('Passwd_Factory_Driver')->create($backend_key);
        } catch (Passwd_Exception $e) {
            Horde::log($e);
            $notification->push(_("Password module is not properly configured"), 'horde.error');
            return;
        }

        try {
            $driver->changePassword(
                $this->_userid,
                $this->_vars->oldpassword,
                $this->_vars->newpassword0
            );
        } catch (Exception $e) {
            $notification->push(sprintf(_("Failure in changing password for %s: %s"), $b_ptr['name'], $e->getMessage()), 'horde.error');
            return;
        }

        $notification->push(sprintf(_("Password changed on %s."), $b_ptr['name']), 'horde.success');

        try {
            Horde::callHook('password_changed', array($this->_userid, $this->_vars->oldpassword, $this->_vars->newpassword0), 'passwd');
        } catch (Horde_Exception_HookNotSet $e) {}

        if (!empty($b_ptr['logout'])) {
            $logout_url = $registry->getLogoutUrl(array(
                'msg' => _("Your password has been succesfully changed. You need to re-login to the system with your new password."),
                'reason' => Horde_Auth::REASON_MESSAGE
            ));
            $registry->clearAuth();
            $logout_url->redirect();
        }

        if ($url = Horde::verifySignedUrl($this->_vars->return_to)) {
            $url = new Horde_Url($url);
            $url->redirect();
        }
    }
}
