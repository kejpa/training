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
                        (string) $request->get('id', ''),
                        (string) $request->get('date', ''),
                        (string) $request->get('length', ''),
                        (string) $request->get('description', ''),
                        $validators);
                break;
            case "PUT":
            case "DELETE":
                $body = json_decode($request->getContent());
                return new SessionForm(
                        (string) $body->id ?? '',
                        (string) $body->date ?? '',
                        (string) $body->length ?? '',
                        (string) $body->description ?? '',
                        $validators);
                break;
        }
    }

}
