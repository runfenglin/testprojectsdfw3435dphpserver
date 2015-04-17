<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;


class ApiKeyAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    protected $userProvider;
    
    public function __construct(ApiKeyUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    
    public function createToken(Request $request, $providerKey)
    {
        //$apiKey = $request->query->get('apikey');
        // $apiKey = $request->headers->get('apikey');
        $apiKey = $request->get('apikey');

        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }
        
        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }
    
    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) 
    {
        $apiKey = $token->getCredentials();
    //    $username = $this->userProvider->getUsernameForApiKey($apiKey);
        
        if (!$apiKey) {
            throw new AuthenticationException(sprintf('API Key "%s" does not exist.', $apiKey));
        }
        
        $user = $this->userProvider->loadUserByApiKey($apiKey);
		
		if (!$user) {
            throw new AuthenticationException(sprintf('API Key "%s" does not exist.', $apiKey));
        }
        
        return new PreAuthenticatedToken($user, $apiKey, $providerKey, $user->getRoles());
    }
    
    public function supportsToken(TokenInterface $token, $providerKey)
    {
		// Make sure that this method returns TRUE for a token that has been
		// created by createToken().
		
        return $token instanceof PreAuthenticatedToken 
                   && $token->getProviderKey() === $providerKey;
    }
	
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		return new Response('Authentication Failed', Response::HTTP_FORBIDDEN);
	}
}
