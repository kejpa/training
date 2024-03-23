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

    static public function createFromContent(Request $request, array $validators): SessionForm {
        $content = json_decode($request->getContent());
        $id = "";
        $rpe = null;
        if (isset($content->id)) {
            $id = $content->id;
        }
        if (isset($content->rpe)) {
            $rpe = (int) $content->rpe;
        }

        return new SessionForm(
                (string) $request->query->get('id', ''),
                (string) $id,
                (string) $content->date ?? '',
                (string) $content->length ?? '',
                (string) $content->description ?? '',
                $rpe,
                $validators);
    }
}
