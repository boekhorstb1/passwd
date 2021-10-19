<?php
namespace Horde\Passwd\Handler\Api;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use \Passwd_Driver as Driver;
use \Horde\Core\Config\State as Configuration;
use \Horde_Registry;






// this is for testing and should be removed
use \Horde_Session;



/**
 * Implementing psr-7
 */
class ChangePassword implements RequestHandlerInterface
{

    protected ResponseFactoryInterface $responseFactory;
    protected StreamFactoryInterface $streamFactory;
    private Driver $driver;
    public Configuration $config;
    private Horde_Registry $registry;
    public $reason;
    public $status;


    // this is for testing and should be removed
    protected Horde_Session $session;
    


   
    public function __construct(
        Horde_Session $session, // this is for testing and should be removed
        ResponseFactoryInterface $responseFactory,
        StreamFactoryInterface $streamFactory,
        Driver $driver,
        Configuration $config,
        Horde_Registry $registry 
    )
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
        $this->driver = $driver;
        $this->config = $config;
        $this->registry = $registry;
        // below is for testing and should be removed
        $this->session = $session;
    }

    /**
     * Handle a request
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        
        // fertig? mach unittests für den Controller (probleme mit Globals? Mach Mock vom Inhalt von Globals)
        
        
        // testing request object
        $testObjectFromRequest = [
            "username" => "charles",
            "oldPassword" => "test123",
            "newPassword" => "test123",
            "newPasswordConfirm" => "dfdfdf"
        ];
        

        $testObjectFromRequest = json_encode($testObjectFromRequest);

        $token = $this->session->getToken();

        /**
         * Real code for later, now testing with uncommented code below
         */
        
        // $post = $request->getParsedBody();


        $post = json_decode($testObjectFromRequest);
        $user = $post->username;
        $currentPassword = $post->oldPassword;
        $newPassword = $post->newPassword;
        $confirmPassword = $post->newPasswordConfirm;

        $jsonData = ['success' => false, 'message' => ''];

        if ($this->verifyPassword($user, $confirmPassword, $currentPassword, $newPassword)) {
            // print_r("working out");
            try {
                $this->driver->changePassword($user, $currentPassword, $newPassword);
                $jsonData['success'] = true;
                // $jsonData['statuscode'] = $this->status;
            } catch (\Throwable $th) {
                $jsonData['message'] = $th->getMessage();
                $jsonData['success'] = false;
                // $jsonData['statuscode'] = 404;
            }
        }
         else  {
            $jsonData['message'] = $this->reason;
            $jsonData['success'] = false;
            // print_r("something is bad");
            // $jsonData['statuscode'] = $this->status;
        }

        

        $jsonString = json_encode($jsonData);

        
        // sending the response object
        $body = $this->streamFactory->createStream($jsonString);
        $response = $this->responseFactory->createResponse($this->status, $this->reason)->withBody($body)
        ->withHeader('Horde-Session-Token', $token)
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($this->status, $this->reason);

        // For debuggin use: \Horde::debug($response, '/dev/shm/test2', false);
        return $response;

        
    }
    
    /**
     * @param string $backend_key  Backend key.
     */
    private function verifyPassword($user, $confirmPassword, $currentPassword, $newPassword )
    {
        
        // Implementiere Checks von basic.php: Extra Notizen mit was noch angepasst werden muss (TODO) (auf English)
          
        
        $conf = $this->config->toArray();
        $registry = $this->registry;
        $userid = $registry->getAuth();
        $credentials = $registry->getAuthCredential();
        $userPassword = (string) $credentials['password'];

        // output if all below checks pass
        $output = true;
        $this->reason = "";
        $this->status = 200;
        
       
        // check if the username is the correct username... users can only change their own passwords right?
        if ($userid !== $user){
            $this->reason = "You can't change password for user ".$user.". Please enter your own correct username.";
            $this->status = (int) 403;
            $output = false;
            return;
        }
        
        // Check for users that cannot change their passwords.
        if (in_array($userid, $conf['user']['refused'])) {
            $this->reason = "You can't change password for user ".$user."";
            $this->status = (int) 403;
            $output = false;
            return;
        }   
        
        // Check that oldpassword is current password
        // print_r($userPassword);

        if ($currentPassword !== $userPassword) {
            $this->reason = "Please enter your current password correctly";
            $this->status = (int) 404;
            $output = false;
            return;
        }

        // Check that the new password is typed correctly
        if ($newPassword !== $confirmPassword){
            $this->reason = "Please make sure you enter your new password correctly";
            $this->status = (int) 404;
            $output = false;
            return;
        }
        

        return $output;

        // other checks are in basic.php, will try to take over as many as possible
       
    }

     
}
