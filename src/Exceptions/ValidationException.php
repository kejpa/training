<?php

declare (strict_types=1);

namespace trainingAPI\Exceptions;

/**
 * Description of ValidationException
 *
 * @author kjell
 */
final class ValidationException extends \InvalidArgumentException{
    private $messages=[];
    
    public static function withMessages(array $messages):self {
        $me=new ValidationException();
        $me->messages=$messages;
        return $me;
    }
    public function getAllMessages():array {
        return $this->messages;
    }
}
