<?php
namespace AppBundle\Services\Security;

use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class AccessDeniedHandler implements AccessDeniedHandlerInterface,AuthenticationFailureHandlerInterface
{
    public function handle(Request $request, AccessDeniedException $accessDeniedException){
        if($request->isXmlHttpRequest())
            return new Response('Denied',403);
        else
            return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception){
        if($request->isXmlHttpRequest())
            return new Response('Denied',403);
        else
            return null;
    }
}
