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
        $id = "";
        if (isset($content->id)) {
            $id = $content->id;
        }
        return new SessionForm(
                (string) $request->query->get('id', ''),
                (string) $id,
                (string) $content->date ?? '',
                (string) $content->length ?? '',
                (string) $content->description ?? '',
                $validators);
    }

}
