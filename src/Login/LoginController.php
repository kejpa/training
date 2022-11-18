<?php

declare (strict_types=1);

namespace trainingAPI\Login;

use DateTimeImmutable;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of LoginController
 *
 * @author kjell
 */
final class LoginController {

    private $userRepository;
    private $loginHandler;
    private $emailExistsQuery;

    public function __construct(UserRepository $userRepository, LoginHandler $loginHandler, EmailExistsQuery $emailExistsQuery) {
        $this->userRepository = $userRepository;
        $this->loginHandler = $loginHandler;
        $this->emailExistsQuery = $emailExistsQuery;
    }

    public function getUserByToken(string $token): ?User {
        return $this->userRepository->getUserByToken($token);
    }

    public function logIn(Request $request): JsonResponse {
        $content = json_decode($request->getContent());
        $username = $content->username;
        $password = $content->password;

        $user = $this->loginHandler->handle(
                new Login($username, $password)
        );

        $origin = $request->headers->get('Origin', "*");
        $headers = [];
        $headers["Access-Control-Allow-Origin"] = $origin;

        if (!$user) {
            $err = new stdClass();
            $err->message = ['Validation failed', "Unable to login"];
            return new JsonResponse($err, 405, $headers);
        }

        foreach ($user->getRecordedEvents() as $event) {
            if ($event instanceof UserWasLoggedIn) {
                $out = new stdClass();
                $out->user = $user; //->jsonSerialize();
                return new JsonResponse($out, 200, $headers);
            }
        }

        $err = new stdClass();
        $err->message = ['Validation failed', "Unable to login"];
        return new JsonResponse($err, 405, $headers);
    }

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
        // . i URL-parametrar fungerar inte i php:s inbyggda webbserver
        $user = str_replace("*", ".", $request->query->get('user'));
        $username = filter_var($user, FILTER_VALIDATE_EMAIL) . "";

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
            return new JsonResponse($err, 403);
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
        // OBS!!!! Ta bort när en "riktig" webbserver används!!!
        // . i URL-parametrar fungerar inte i php:s inbyggda webbserver
        $username = str_replace("*", ".", $request->query->get('user'));
        $username = filter_var($username, FILTER_VALIDATE_EMAIL) . "";

        if (!$user || $user->getToken() !== $userToken || $user->getEmail() !== $username) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 405);
        }

        $password = $request->request->get("password");
        $passwordValidator = PasswordValidatorFactory::createPasswordValidator();
        if (!$passwordValidator->validate($password)) {
            $err = new stdClass();
            $err->message = array_merge(['Validation failed'], $passwordValidator->getErrors());
            return new JsonResponse($err, 403);
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
            return new JsonResponse($err, 405, $headers);
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
