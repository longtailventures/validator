<?php

namespace LongTailVentures\Validator;

class Vin extends \Zend\Validator\AbstractValidator
{
    const INVALID_LENGTH = 'invalid_length';
    const ALL_ZERO_ONE_DIGITS = 'all_zero_one_digits';
    const INVALID_VIN = 'invalid_vin';
    const INVALID_CHARS = 'invalid_chars';

    protected $messageTemplates = array(
        self::INVALID_LENGTH => "The vin is an invalid length",
        self::ALL_ZERO_ONE_DIGITS => "The vin contains all zeros or ones",
        self::INVALID_CHARS => "The vin contains invalid characters",
        self::INVALID_VIN => "The vin fails the character weight checksum"
    );

    public function isValid($vin)
    {
        $isValid = false;

        // if the vin isn't the correct length, no need to go any further
        if (strlen($vin) != 17)
        {
            $this->error(self::INVALID_LENGTH);
            return false;
        }

        // also check against all 0's and all 1's, which validate but are fake
        if ($vin == '11111111111111111' or $vin == '00000000000000000')
        {
            $this->error(self::ALL_ZERO_ONE_DIGITS);
            return false;
        }

        // also check invalid chars
        if (stripos($vin, 'I') !== false || stripos($vin, 'Q') !== false || stripos($vin, 'O') !== false)
        {
            $this->error(self::INVALID_CHARS);
            return false;
        }

        // list of letter => number values used in vin decoding
        $values = array(
            'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6,
            'g' => 7, 'h' => 8, 'j' => 1, 'k' => 2, 'l' => 3, 'm' => 4,
            'n' => 5, 'p' => 7, 'r' => 9, 's' => 2, 't' => 3, 'u' => 4,
            'v' => 5, 'w' => 6, 'x' => 7, 'y' => 8, 'z' => 9
        );
        // list of vin character position weights (including check digit)
        $weights = array(8,7,6,5,4,3,2,10,0,9,8,7,6,5,4,3,2);

        // split vin into an array by character
        $vinValues = str_split(strtolower($vin));
        $vinCalc = 0;

        // calculate a running sum of the products of each character in the vin and
        // their corresponding weight value. If the character is a number, use the
        // character directly; otherwise find its value in the $values array and use that.
        foreach ($vinValues as $index => $val)
        {
            if (is_numeric($val))
            {
                if (!array_key_exists($index, $weights))
                {
                    $this->error(self::INVALID_VIN);
                    return false;
                }
                $vinCalc += $val*$weights[$index];
            }
            else
            {
                if (!array_key_exists($val, $values))
                {
                    $this->error(self::INVALID_VIN);
                    return false;
                }
                $vinCalc += $values[$val]*$weights[$index];
            }
        }

        // now divide the total obtained by 11
        // and obtain the remainder - it should equal the check digit
        $remainder = $vinCalc % 11;

        // if the remainder is 10 then the check digit should be 'X'
        if ($remainder == 10 and $vinValues[8] == 'x')
            return true;
        else if ($remainder == $vinValues[8])
            return true;
        else
        {
            $this->error(self::INVALID_VIN);
            return false;
        }
    }
}
