<?php

namespace LongTailVentures\Validator;

class PhoneNumber extends \Zend\Validator\AbstractValidator
{
    const INVALID_DIGIT = '3';
    const LENGTH = '1';
    const SAME_DIGITS = '2';

    protected $messageTemplates = array(
        self::INVALID_DIGIT => "The input contains an invalid digit",
        self::LENGTH  => "The input should have a length of 10 characters",
        self::SAME_DIGITS => "The input should should be valid 10 digit phone number"
    );

    public function isValid($phoneNumber)
    {
        $isValid = false;

        $phoneNumber = preg_replace('/[^\d]/', '', trim($phoneNumber));

        if (strlen($phoneNumber) >= 10)
        {
            // same digit (naive) matching. Grab first digit, do a str_replace with blank string, if length is zero then
            // we know phone numbers are the same digit
            $firstDigit = substr($phoneNumber, 0, 1);
            $phoneNumber = str_replace($firstDigit, '', $phoneNumber);
            if (strlen($phoneNumber) > 0)
                $isValid = true;
            else
                $this->error(self::SAME_DIGITS);
        }
        else if (strlen($phoneNumber) < 10)
            $this->error(self::LENGTH);
        else
            $this->error(self::INVALID_DIGIT);

        return $isValid;
    }
}
