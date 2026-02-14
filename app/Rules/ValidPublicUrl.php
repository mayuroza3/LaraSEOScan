<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidPublicUrl implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            $fail('The :attribute must be a valid URL.');
            return;
        }

        $host = parse_url($value, PHP_URL_HOST);

        if (!$host) {
            $fail('The :attribute must have a valid host.');
            return;
        }

        // Check for localhost
        if (in_array(strtolower($host), ['localhost', '127.0.0.1'])) {
            $fail('The :attribute cannot be a localhost address.');
            return;
        }

        // Check for private IPs
        $ip = gethostbyname($host);
        
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $fail('The :attribute cannot be a private or reserved IP address.');
            return;
        }
    }
}
