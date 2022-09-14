<?php

namespace trainingAPI\Framework\ChainOfResponse;

/**
 *
 * @author kjell
 */
interface ChainOfResponseValidator {
    public function validate(string $param): bool;
    public function getErrors(): array;
}
