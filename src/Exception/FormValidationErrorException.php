<?php

namespace App\Exception;

use Symfony\Component\Validator\ConstraintViolationList;
use Throwable;

class FormValidationErrorException extends \Exception
{
    /**
     * @var ConstraintViolationList
     */
    private $constraintViolationList;

    public function __construct(ConstraintViolationList $constraintViolationList, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->constraintViolationList = $constraintViolationList;
    }

    public function getConstraintViolationList(): ConstraintViolationList
    {
        return $this->constraintViolationList;
    }
}
