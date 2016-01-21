<?php

namespace LongTailVentures\Validator;

class PostCode extends \Zend\Validator\AbstractValidator
{
    const INVALID_POSTAL_CODE = 'invalid_postal_code';
    const INVALID_ZIP_CODE = 'invalid_zip_code';

    protected $messageTemplates = array(
        self::INVALID_POSTAL_CODE => "The input is an invalid postal code",
        self::INVALID_ZIP_CODE => "The input is an invalid zip code"
    );

    public function isValid($postCode)
    {
        $postCode = preg_replace('/\s*/m', '', $postCode);

        $zipCodeValidator = new \LongTailVentures\Validator\ZipCode();

        $isValidZipCode = $zipCodeValidator->isValid($postCode);
        $isValidPostalCode = preg_match("/^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$/i", $postCode);

        if ($isValidZipCode || $isValidPostalCode)
            return true;

        if (!$isValidZipCode)
        {
            $this->error(self::INVALID_ZIP_CODE);
            return false;
        }

        if (!$isValidPostalCode)
        {
            $this->error(self::INVALID_POSTAL_CODE);
            return false;
        }
    }
}
