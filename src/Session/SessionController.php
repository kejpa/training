<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Login\LoginController;

/**
 * Description of SessionController
 *
 * @author kjell
 */
final class SessionController {

    private $loginController;
    private $sessionRepository;

    public function __construct(LoginController $loginController, SessionRepository $sessionRepository) {
        $this->loginController = $loginController;
        $this->sessionRepository = $sessionRepository;
    }

    public function getAllSessions(Request $request): JsonResponse {
        $userToken = $request->headers->get("user-token") ?? "Abcd1234";
        $user = $this->loginController->getUserByToken($userToken);

        if (!$user || $user->getToken() !== $userToken) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 405);
        }

        $out = new stdClass();
        $out->sessions = $this->sessionRepository->getAllSessions($user->getId());

        return new JsonResponse($out, 200);
    }

    public function getSession(Request $request, array $param): JsonResponse {
        $userToken = $request->headers->get("user-token") ?? "Abcd1234";
        $user = $this->loginController->getUserByToken($userToken);

        if (!$user || $user->getToken() !== $userToken) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 405);
        }

        if (array_key_exists('id', $param)) {
            $id = filter_var($param['id'], FILTER_VALIDATE_INT);
        }
        if ($id === false) {
            $err = new stdClass();
            $err->message = ['Validation failed', "Invalid id supplied"];
            return new JsonResponse($err, 400);
        }

        $out = new stdClass();
        $out->sessions = $this->sessionRepository->getSession($user->getId(), $id);

        return new JsonResponse($out, 200);
    }

    public function addSession(Request $request) {
        $userToken = $request->headers->get("user-token") ?? "Abcd1234";
        $user = $this->loginController->getUserByToken($userToken);

        if (!$user || $user->getToken() !== $userToken) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 405);
        }

        $validators = ["date" => sessionValidatorFactory::createSessionDateValidator()];
//        $validators["length"]=sessionValidatorFactory::createSessionLengthValidator();
        $form = SessionFormFactory::createFromRequest($request, $validators);

        if ($form->hasValidationErrors()) {
            $err = new stdClass();
            $err->message = $form->getValidationErrors();
            return new JsonResponse($err, 405);
        }

        try {
            $session = $form->toCommand($user->getId());
            $id = $this->sessionRepository->addSession($user->getId(), $session);
            $session->setId($id);

            $out = new stdClass();
            $out->session = $session;
            return new JsonResponse($out);
        } catch (Exception $ex) {
            $err = new stdClass();
            $err->message = $ex->getMessage();
            return new JsonResponse($err, 400);
        }
    }

}
