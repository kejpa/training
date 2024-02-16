<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use Exception;
use stdClass;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use trainingAPI\Exceptions\ValidationException;
use trainingAPI\Framework\ChainOfResponse\Validator\IdExistsValidator;
use trainingAPI\Login\Authenticator;

/**
 * Description of SessionController
 *
 * @author kjell
 */
final class SessionController {

    public function __construct(private Authenticator $authenticator, private SessionRepository $sessionRepository, private IdExistsValidator $idExistsValidator) {
        
    }

    public function getAllSessions(): JsonResponse {
        $user = $this->authenticator->authenticate();

        $out = new stdClass();
        $out->sessions = $this->sessionRepository->getAllSessions($user->getId());

        return (new JsonResponse($out, 200))->setEncodingOptions(15 + JSON_UNESCAPED_UNICODE);
    }

    public function getSession(Request $request, array $param): JsonResponse {
        $user = $this->authenticator->authenticate();
        $id = false;

        if (array_key_exists('id', $param)) {
            $id = filter_var($param['id'], FILTER_VALIDATE_INT);
        }
        if ($id === false) {
            $messages = ['Validation failed', "Invalid id supplied"];
            throw ValidationException::withMessages($messages);
        }

        $out = new stdClass();
        $out->sessions = $this->sessionRepository->getSession($user->getId(), $id);

        return new JsonResponse($out, 200);
    }

    public function addSession(Request $request) {
        $user = $this->authenticator->authenticate();

        $validators = ["date" => SessionValidatorFactory::createSessionDateValidator()];
        $form = SessionFormFactory::createFromContent($request, $validators);

        $session = $form->toCommand($user->getId());
        $id = $this->sessionRepository->addSession($user->getId(), $session);
        $session->setId($id);

        $out = new stdClass();
        $out->session = $session;
        return new JsonResponse($out, 200);
    }

    public function updateSession(Request $request, array $param) {
        $user = $this->authenticator->authenticate();
        $id = false;

        if (array_key_exists('id', $param)) {
            $id = filter_var($param['id'], FILTER_VALIDATE_INT);
        }
        if ($id === false) {
            $messages = ['Validation failed', "Invalid id supplied"];
            throw ValidationException::withMessages($messages);
        }

        $request->query->add($param);

        $validators = ["date" => SessionValidatorFactory::createSessionDateValidator()];
        $validators["id"] = SessionValidatorFactory::createSessionIdValidator($user->getId(), $this->idExistsValidator);
        $form = SessionFormFactory::createFromContent($request, $validators);

        $form->validate();

        $session = $form->toCommand($user->getId());
        $session->setId($id);
        $rows = $this->sessionRepository->updateSession($user->getId(), $session);

        $out = new stdClass();
        $out->rowsAffected = $rows;
        $out->session = $session;

        return new JsonResponse($out);
    }

    public function deleteSession(Request $request, array $param) {
        $user = $this->authenticator->authenticate();
        $id = false;

        if (!$user || $user->getToken() !== $userToken) {
            $err = new stdClass();
            $err->message = ['Validation failed', "No match for token $userToken"];
            return new JsonResponse($err, 401);
        }

        if (array_key_exists('id', $param)) {
            $id = filter_var($param['id'], FILTER_VALIDATE_INT);
        }
        if ($id === false) {
            $err = new stdClass();
            $err->message = ['Validation failed', "Invalid id supplied"];
            return new JsonResponse($err, 400);
        }

        try {
            $rows = $this->sessionRepository->deleteSession($id, $user->getId());

            $out = new stdClass();
            $out->rowsAffected = $rows;
            return new JsonResponse($out);
        } catch (Exception $ex) {
            $err = new stdClass();
            $err->message = $ex->getMessage();
            return new JsonResponse($err, 400);
        }
    }
}
