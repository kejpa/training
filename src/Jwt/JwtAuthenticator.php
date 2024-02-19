<?php

declare (strict_types=1);

namespace trainingAPI\Jwt;

use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Exceptions\AuthenticationException;
use trainingAPI\Jwt\JwtAccessTokenHandler;
use trainingAPI\Login\Authenticator;
use trainingAPI\Login\User;
use trainingAPI\Login\UserRepository;

/**
 * Description of jsonAuthenticator
 *
 * @author kjell
 */
final class JwtAuthenticator implements Authenticator {

    public function __construct(private JwtAccessTokenHandler $jwtAccessTokenHandler, private UserRepository $userRepository) {
        
    }

    public function authenticate(Request $request): User {

        $token = $request->headers->get('Token');
        if (!$token) {
            throw new AuthenticationException("Bad or missing 'Token' header");
        }

        preg_match('/Bearer\s(\S+)/', $token, $matches);
        if (!isset($matches[1]) or strlen($matches[1]) === 0) {
            
        }
        $jwtToken = $matches[1];

        if (!$this->jwtAccessTokenHandler->validate($jwtToken)) {
            throw new AuthenticationException("Bad jwt token");
        }

        $data = json_decode($this->jwtAccessTokenHandler->getPayload($jwtToken));

        return $this->userRepository->getUserByEmail($data->email);
    }
}
