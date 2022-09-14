<?php

declare (strict_types=1);

namespace trainingAPI\Session;

/**
 * Description of SessionFormFactory
 *
 * @author kjell
 */
final class SessionFormFactory {

    static public function createFromRequest($request, array $validators): SessionForm {
        return new SessionForm(
                (string) $request->get('id', ''),
                (string) $request->get('date', ''),
                (string) $request->get('length', ''),
                (string) $request->get('description', ''),
                $validators
        );
    }
}
