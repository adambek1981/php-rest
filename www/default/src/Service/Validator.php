<?php

namespace App\Service;

use Symfony\Component\Validator\Constraint as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class Validator
{
    /** @var ValidatorInterface $validator */
    private $validator;

    /**
     * this class constructor
     */
    public function __construct()
    {
        $this->validator = (new ValidatorBuilder())->getValidator();
    }

    /**
     * @param mixed $data
     * @param Assert|Assert[] $assert
     */
    public function validate($data, $assert)
    {
        $baseValidator = (new ValidatorBuilder())->getValidator();
        $errors = $baseValidator->validate($data, $assert);

        if (0 < count($errors)) {
            throw new \UnexpectedValueException("Validation error:\n" . (string)$errors);
        }
    }
}
