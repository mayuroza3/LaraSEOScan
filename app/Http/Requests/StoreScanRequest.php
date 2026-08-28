<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'type' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $url = $this->input('url');
            if ($url) {
                $host = parse_url($url, PHP_URL_HOST);
                if ($host) {
                    $hostLower = strtolower($host);
                    if (in_array($hostLower, ['localhost', '127.0.0.1', '[::1]', 'localhost.localdomain'])) {
                        $validator->errors()->add('url', '🚫 Scanning local or internal hostnames is restricted.');
                        return;
                    }
                    
                    $ip = gethostbyname($host);
                    if ($ip && $ip !== $host) {
                        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                            $validator->errors()->add('url', '🚫 Scans are restricted to public web domains. Scanning internal network ranges is blocked.');
                        }
                    }
                }
            }
        });
    }
}
