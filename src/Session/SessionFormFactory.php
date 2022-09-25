<?php

declare (strict_types=1);

namespace trainingAPI\Session;

use Symfony\Component\HttpFoundation\Request;

/**
 * Description of SessionFormFactory
 *
 * @author kjell
 */
final class SessionFormFactory {

    static public function createFromRequest(Request $request, array $validators): SessionForm {
        switch ($request->getMethod()) {
            case "POST":
            case "GET":
                return new SessionForm(
                        (string) $request->query->get('id', ''),
                        (string) $request->request->get('id', ''),
                        (string) $request->request->get('date', ''),
                        (string) $request->request->get('length', ''),
                        (string) $request->request->get('description', ''),
                        $validators);
                break;
            case "PUT":
            case "DELETE":
                $body = json_decode($request->getContent());
                return new SessionForm(
                        (string) $request->query->get('id', ''),
                        (string) $body->id ?? '',
                        (string) $body->date ?? '',
                        (string) $body->length ?? '',
                        (string) $body->description ?? '',
                        $validators);
                break;
        }
    }

}
