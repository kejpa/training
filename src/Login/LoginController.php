<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use DateTimeImmutable;
use stdClass;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Exceptions\AuthenticationException;
use trainingAPI\Jwt\JwtAccessTokenHandler;
use trainingAPI\Jwt\JwtRefreshTokenHandler;
use trainingAPI\Jwt\RefreshToken;

/**
 * Description of LoginController
 *
 * @author kjell
 */
final class LoginController {

    public function __construct(private Request $request, private UserRepository $userRepository, private LoginHandler $loginHandler,
            private EmailExistsQuery $emailExistsQuery, private JwtAccessTokenHandler $jwtAccessTokenHandler, private Authenticator $authenticator,
            private JwtRefreshTokenHandler $jwtRefreshTokenHandler, private \trainingAPI\Jwt\TokenRepository $tokenRepository) {
        
    }

    public function logIn(Request $request): JsonResponse {
        $content = json_decode($request->getContent());
        $username = $content->username ?? '';
        $password = $content->password ?? '';

        $user = $this->loginHandler->handle(new Login($username, $password));

        $jwt = $this->jwtAccessTokenHandler->getToken(json_encode($user));

        $out = new stdClass();
        $out->jwt = $jwt;

        $refreshToken = new RefreshToken($user->getId(), bin2hex(random_bytes(20)));
        $cookieContent = $this->jwtRefreshTokenHandler->getToken(json_encode($refreshToken));
        $cookie = $this->createCookie($cookieContent, $this->jwtRefreshTokenHandler->getExpires());

        // Skapa post i databasen 
        $refreshToken->setExpires($this->jwtRefreshTokenHandler->getExpires());
        $this->tokenRepository->addRefreshToken($refreshToken);

        $retur = new JsonResponse($out);
        $retur->headers->setCookie($cookie);
        return $retur;
    }

    public function check(Request $request): JsonResponse {
        $user = $this->authenticator->authenticate($request);

        $jwt = $this->jwtAccessTokenHandler->getToken(json_encode($user));

        $out = new stdClass();
        $out->jwt = $jwt;
        return new JsonResponse($out);
    }

    public function logout(Request $request): JsonResponse {
        try {
            $cookie = $this->createCookie('', 0);
            $refreshToken = $this->getRefreshToken($request);

            $this->tokenRepository->removeRefreshToken($refreshToken);
        } finally {
            $out = new stdClass();
            $out->message = ['User logged out'];

            $retur = new JsonResponse($out);
            $retur->headers->setCookie($cookie);
            return $retur;
        }
    }

    public function refresh(Request $request): JsonResponse {
        $refreshToken = $this->getRefreshToken($request);

        // Kontrollera och hämta användare
        $user = $this->getUserFromRefreshToken($refreshToken);
        $jwt = $this->jwtAccessTokenHandler->getToken(json_encode($user));

        $out = new stdClass();
        $out->jwt = $jwt;

        $cookieContent = $this->jwtRefreshTokenHandler->getToken(json_encode($refreshToken));
        $cookie = $this->createCookie($cookieContent, $this->jwtRefreshTokenHandler->getExpires());

        // Uppdatera databasen med nya expires
        $refreshToken->setExpires($this->jwtRefreshTokenHandler->getExpires());
        $this->tokenRepository->updateRefreshToken($refreshToken);

        $retur = new JsonResponse($out);
        $retur->headers->setCookie($cookie);
        return $retur;
    }

    /*
     * Privata funktioner
     */

    private function getRefreshToken(Request $request): RefreshToken {
        // Läs cookie
        $refreshToken = $request->cookies->getString("refresh", '');
        if ($refreshToken === '') {
            throw new AuthenticationException('No refresh token');
        }
        // Validera token
        $this->jwtRefreshTokenHandler->validate($refreshToken);

        // Läs payload 
        $payload = $this->jwtRefreshTokenHandler->getPayload($refreshToken);

        // Returnera token
        return RefreshToken::fromStdClass(json_decode($payload));
    }

    private function getUserFromRefreshToken(RefreshToken $token): User {
        // Kontrollera användare
        $user = $this->userRepository->getUserByRefreshToken($token);

        return $user;
    }

    private function createCookie(string $content, int $expires) {
        return Cookie::create("refresh", $content, $expires, "/api/refresh", secure: true, httpOnly: true); //, sameSite: Cookie::SAMESITE_STRICT);
    }

    /*
     * Gammalt!
     */

    public function resetPassword(Request $request, array $param): JsonResponse {
        $request->query->add($param);
        // OBS!!!! Ta bort när en "riktig" webbserver används!!!
        // . i URL-parametrar fungerar inte i php:s inbyggda webbserver
        $user = str_replace("*", ".", $request->query->get('user'));
        $username = filter_var($user, FILTER_VALIDATE_EMAIL) . "";

        $user = $this->loginHandler->handle(new Login($username, ""));

        if (!$user) {
            $err = new stdClass();
            $err->message = ['Validation failed', "User not found ($username)"];
            return new JsonResponse($err, 400);
        }

        $user->forgot();
        $this->userRepository->updateUser($user);

        $out = new stdClass();
        $out->passwordReset = true;
        return new JsonResponse($out);
    }

    public function changePassword(Request $request, array $param): JsonResponse {
        $request->query->add($param);
        // OBS!!!! Ta bort när en "riktig" webbserver används!!!
        // . i URL-parametrar fungerar inte @ i php:s inbyggda webbserver
        //$user = str_replace("*", ".", $request->query->get('user'));
        //$username = filter_var($user, FILTER_VALIDATE_EMAIL) . "";
        $username = filter_input(INPUT_POST, 'user', FILTER_VALIDATE_EMAIL) . "";

        $user = $this->loginHandler->handle(new Login($username, ""));
        $resetToken = $request->request->get("resetToken");

        if (!$user) {
            $err = new stdClass();
            $err->message = ['Validation failed', "User not found ($username)"];
            return new JsonResponse($err, 400);
        }

        if ($resetToken !== $user->getResetToken() ||
                $user->getResetDate() === null ||
                $user->getResetDate()->diff(new DateTimeImmutable(), false)->format('%R%a') > 0) {
            $err = new stdClass();
            $err->message = ['Validation failed', "Token don't match"];
            return new JsonResponse($err, 400);
        }

        $password = $request->request->get("password");
        $passwordValidator = PasswordValidatorFactory::createPasswordValidator();
        if (!$passwordValidator->validate($password)) {
            $err = new stdClass();
            $err->message = array_merge(['Validation failed'], $passwordValidator->getErrors());
            return new JsonResponse($err, 400);
        }

        $user->changePassword($password);
        $rowsAffected = $this->userRepository->updateUser($user);

        $out = new stdClass();
        $out->rowsAffected = $rowsAffected;
        $out->user = $user;

        return new JsonResponse($out);
    }

    public function updatePassword(Request $request, array $param): JsonResponse {
        $request->query->add($param);
        $userToken = $request->headers->get("user-token") ?? "";
        $user = $this->getUserByToken($userToken);

        $username = $request->query->get('user');
        $username = filter_var($username, FILTER_VALIDATE_EMAIL) . "";

        if (!$user || $user->getToken() !== $userToken || $user->getEmail() !== $username) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 400);
        }

        $password = $request->request->get("password");
        $passwordValidator = PasswordValidatorFactory::createPasswordValidator();
        if (!$passwordValidator->validate($password)) {
            $err = new stdClass();
            $err->message = array_merge(['Validation failed'], $passwordValidator->getErrors());
            return new JsonResponse($err, 400);
        }

        $user->changePassword($password);
        $rowsAffected = $this->userRepository->updateUser($user);

        $out = new stdClass();
        $out->rowsAffected = $rowsAffected;
        $out->user = $user;

        return new JsonResponse($out);
    }

    public function checkToken(Request $request) {
        $userToken = $request->headers->get("user-token") ?? "";
        $user = $this->getUserByToken($userToken);
        $origin = $request->headers->get('Origin', "*");
        $headers = [];
        $headers["Access-Control-Allow-Origin"] = $origin;

        if ($user === null) {
            $err = new stdClass();
            $err->message = ['Invalid token'];
            return new JsonResponse($err, 401, $headers);
        }

        $out = new stdClass();
        $out->user = $user;

        return new JsonResponse($out, 200, $headers);
    }

    public function register(Request $request) {
        $validators["email"] = EmailValidatorFactory::createEmailDontExistsValidator($this->emailExistsQuery);
        $validators["password"] = PasswordValidatorFactory::createPasswordValidator();

        $userForm = UserForm::fromRequest($request->request->all(), $validators);

        if ($userForm->hasValidationErrors()) {
            $err = new stdClass();
            $err->message = array_merge(['Validation failed'], $userForm->getValidationErrors());
            return new JsonResponse($err, 400);
        }

        $user = $userForm->toCommand();
        $user->setId($this->userRepository->addUser($user));

        $out = new stdClass();
        $out->user = $user;

        return new JsonResponse($out);
    }
}
