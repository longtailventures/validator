<?php

namespace LongTailVentures\Validator;

class ZipCode extends \Zend\Validator\AbstractValidator
{
    const INVALID_CHARACTER = '3';
    const LENGTH = '1';

    protected $messageTemplates = array(
        self::INVALID_CHARACTER => "The input contains an invalid character",
        self::LENGTH  => "The input be 5 characters"
    );

    public function isValid($zipCode)
    {
        $isValid = false;

        $zipCode = preg_replace('/\s*/m', '', $zipCode);
        if (preg_match("/^([0-9]{5})$/i", $zipCode))
            return true;
        else if (strlen($zipCode) != 5)
            $this->error(self::LENGTH);
        else
            $this->error(self::INVALID_CHARACTER);

        return $isValid;
    }
}