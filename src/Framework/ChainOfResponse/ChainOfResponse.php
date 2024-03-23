<?php

namespace trainingAPI\Framework\ChainOfResponse;

/**
 *
 * @author kjell
 */
interface ChainOfResponse {

    public function validate(string $param): bool;

    public function getErrors(): array;
}
