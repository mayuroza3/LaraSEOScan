<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Seo\SafeUrlService;

class SafeUrlServiceTest extends TestCase
{
    public function test_valid_public_urls_pass()
    {
        $this->assertTrue(SafeUrlService::isSafeUrl('https://example.com'));
        $this->assertTrue(SafeUrlService::isSafeUrl('http://google.com'));
    }

    public function test_private_ipv4_and_loopback_blocked()
    {
        $this->assertFalse(SafeUrlService::isSafeUrl('http://127.0.0.1'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://127.0.0.1:8080/admin'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://10.0.0.1'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://192.168.1.100'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://172.16.0.5'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://localhost'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://localhost:3000'));
    }

    public function test_cloud_metadata_endpoints_blocked()
    {
        $this->assertFalse(SafeUrlService::isSafeUrl('http://169.254.169.254'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://169.254.169.254/latest/meta-data/'));
    }

    public function test_ipv6_loopback_and_link_local_blocked()
    {
        $this->assertFalse(SafeUrlService::isSafeUrl('http://[::1]'));
        $this->assertFalse(SafeUrlService::isSafeUrl('http://[fe80::1]'));
    }

    public function test_invalid_schemes_blocked()
    {
        $this->assertFalse(SafeUrlService::isSafeUrl('file:///etc/passwd'));
        $this->assertFalse(SafeUrlService::isSafeUrl('gopher://127.0.0.1:70'));
        $this->assertFalse(SafeUrlService::isSafeUrl('dict://127.0.0.1:11211'));
        $this->assertFalse(SafeUrlService::isSafeUrl('javascript:alert(1)'));
    }
}
