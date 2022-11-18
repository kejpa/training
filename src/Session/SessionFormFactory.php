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
        $content = json_decode($request->getContent());
        return new SessionForm(
                (string) $request->query->get('id', ''),
                (string) $content->id ?? '',
                (string) $content->date ?? '',
                (string) $content->length ?? '',
                (string) $content->description ?? '',
                $validators);
    }

}
