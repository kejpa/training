<?php

declare (strict_types=1);

namespace trainingAPI\Preflight;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PreflightController {

    public function preFlight(Request $request): JsonResponse {
        $origin = $request->headers->get('Origin');
        $headers = ["Access-Control-Allow-Origin" => $origin,
            "Access-Control-Allow-Methods" => "GET, PUT, POST, DELETE",
            "Access-Control-Allow-Headers" => "content-type, token",
            "Access-Control-Request-Headers" => "content-type, token",
        ];

        return new JsonResponse(null, 200, $headers);
    }

}
