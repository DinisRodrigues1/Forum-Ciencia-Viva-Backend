<?php
namespace app;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


// WORK WORK WORK
class Auth
{

    /**
     * It's only a validation example!
     * You should search user (on your database) by authorization token
     */
    public function getUserByToken($token)
    {

                if ($token != $iden) {
                    /**
                     * The throwable class must implement UnauthorizedExceptionInterface
                     */

                    throw new UnauthorizedException('Invalid Token or permission');
                }

                return $iden;
      //  })

    }

}