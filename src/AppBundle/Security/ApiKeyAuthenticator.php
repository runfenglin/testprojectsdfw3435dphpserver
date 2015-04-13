<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class ApiKeyAuthenticator implements SimplePreAuthenticatiorInterface
{
    protected $userProvider;
    
    public function __construct(ApiKeyUserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }
    
    public function createToken(Request $request, $providerKey)
    {
        $apiKey = $request->query->get('apikey');
        // $apiKey = $request->headers->get('apikey');
        
        if (!$apiKey) {
            throw new BadCredentialsException('No API key found');
        }
        
        return new PreAuthenticatedToken(
            'anon.',
            $apiKey,
            $providerKey
        );
    }
    
    public function anthenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey) 
    {
        $apiKey = $token->getCredentials();
        $username = $this->userProvider->getUsernameForApiKey($apiKey);
        
        if (!$username) {
            throw new AuthenticationException(sprintf('API Key "%s" does not exist.', $apiKey));
        }
        
        $user = $this->userProvider->loadUserByUsername($username);
        
        return new PreAuthenticatedToken($user, $apiKey, $providerKey, $user->getRoles());
    }
    
    public function supportsToken(TokenInterface $token, $providerKey)
    {
		// Make sure that this method returns TRUE for a token that has been
		// created by createToken().
		
        return $token instanceof PreAuthenticatedToken 
                   && $token->getProviderKey() === $providerKey;
    }
}
