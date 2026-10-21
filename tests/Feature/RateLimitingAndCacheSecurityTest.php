<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingAndCacheSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_endpoint_enforces_rate_limiting(): void
    {
        // 5 allowed requests per minute
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => 'attacker@test.com',
                'password' => 'wrongpassword',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must trigger 429 Too Many Requests
        $response = $this->post('/login', [
            'email' => 'attacker@test.com',
            'password' => 'wrongpassword',
        ]);
        $response->assertStatus(429);
    }

    public function test_forgot_password_endpoint_enforces_rate_limiting(): void
    {
        // 5 allowed requests per minute
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/forgot-password', [
                'email' => 'user@test.com',
            ]);
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 6th attempt must trigger 429
        $response = $this->post('/forgot-password', [
            'email' => 'user@test.com',
        ]);
        $response->assertStatus(429);
    }

    public function test_authenticated_dashboard_has_strict_anti_cache_headers(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private');
        $response->assertHeader('Pragma', 'no-cache');
        $response->assertHeader('Expires', 'Fri, 01 Jan 1990 00:00:00 GMT');
    }

    public function test_logout_response_breaks_cache_and_clears_site_data(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
        $response->assertHeader('Clear-Site-Data', '"cache", "storage"');
        $response->assertHeader('Cache-Control', 'max-age=0, must-revalidate, no-cache, no-store, private');
        $response->assertHeader('Pragma', 'no-cache');
    }

    public function test_export_pdf_enforces_rate_limiting(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin');

        // 10 requests allowed
        for ($i = 0; $i < 10; $i++) {
            $response = $this->actingAs($admin)->get('/reports/export-pdf');
            $this->assertNotEquals(429, $response->getStatusCode());
        }

        // 11th request must return 429
        $response = $this->actingAs($admin)->get('/reports/export-pdf');
        $response->assertStatus(429);
    }

    public function test_global_per_ip_rate_limiter_enforces_limit_and_cannot_be_bypassed(): void
    {
        $testIp = '198.51.100.42';

        // Perform requests from the specific IP up to limit (120 per minute)
        for ($i = 0; $i < 120; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $testIp])
                ->get('/login');
            $this->assertEquals(200, $response->getStatusCode());
        }

        // 121st request from same IP must be rejected with 429
        $blockedResponse = $this->withServerVariables(['REMOTE_ADDR' => $testIp])
            ->get('/login');
        $blockedResponse->assertStatus(429);

        // A different IP is still allowed and unaffected
        $otherIpResponse = $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.88'])
            ->get('/login');
        $this->assertEquals(200, $otherIpResponse->getStatusCode());
    }

    public function test_login_lockout_enforces_15_minute_lock_and_flashes_countdown(): void
    {
        $attackerIp = '198.51.100.99';

        for ($i = 1; $i <= 4; $i++) {
            $response = $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
                ->post('/login', [
                    'email' => 'victim@test.com',
                    'password' => 'wrong-pass',
                ]);
            $response->assertSessionHasErrors('email');
            $this->assertNull(session('lockout_seconds'));

            // Verifikasi box timer lockout disembunyikan (display:none) pada percobaan ke-1 s.d. 4
            $loginPageView = $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
                ->get('/login');
            $loginPageView->assertSee('id="lockout-timer-box" class="alert alert-danger p-3 mb-4 rounded-3 border-danger shadow-sm" style="display:none;"', false);
        }

        // Percobaan ke-5 gagal -> langsung dikunci 15 menit (900 detik)
        $response5 = $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
            ->post('/login', [
                'email' => 'victim@test.com',
                'password' => 'wrong-pass',
            ]);

        $response5->assertSessionHas('lockout_seconds');
        $lockSec = session('lockout_seconds');
        $this->assertGreaterThan(850, $lockSec);
        $this->assertLessThanOrEqual(900, $lockSec);

        // Percobaan ke-6 dari IP terkunci
        $response6 = $this->withServerVariables(['REMOTE_ADDR' => $attackerIp])
            ->get('/login');
        $response6->assertSee('lockout-countdown');
        $response6->assertSee('Akun / IP Terkunci Sementara');
    }

    public function test_different_devices_are_isolated_and_loopback_ip_does_not_lock_all_devices(): void
    {
        $serverLoopbackIp = '127.0.0.1';
        $device1Token = 'device-token-uuid-111111111111';
        $device2Token = 'device-token-uuid-222222222222';

        // Device 1 fails 5 times
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $serverLoopbackIp])
                ->withCookies(['simaset_did' => $device1Token])
                ->post('/login', [
                    'email' => 'superadmin@simaset.com',
                    'password' => 'wrongpass',
                ]);
        }

        // Device 1 is now locked out
        $device1Login = $this->withServerVariables(['REMOTE_ADDR' => $serverLoopbackIp])
            ->withCookies(['simaset_did' => $device1Token])
            ->get('/login');
        $device1Login->assertSee('lockout-countdown');

        // Device 2 (different device / browser) opens login on the same server IP
        // Must NOT be locked out!
        $device2Login = $this->withServerVariables(['REMOTE_ADDR' => $serverLoopbackIp])
            ->withCookies(['simaset_did' => $device2Token])
            ->get('/login');
        $device2Login->assertSee('id="lockout-timer-box" class="alert alert-danger p-3 mb-4 rounded-3 border-danger shadow-sm" style="display:none;"', false);

        // Device 2 attempts login with wrong password (1st attempt)
        // Must show remaining 4 attempts, NOT lockout!
        $device2Attempt1 = $this->withServerVariables(['REMOTE_ADDR' => $serverLoopbackIp])
            ->withCookies(['simaset_did' => $device2Token])
            ->post('/login', [
                'email' => 'superadmin@simaset.com',
                'password' => 'wrongpass',
            ]);
        $device2Attempt1->assertSessionHasErrors('email');
        $this->assertStringContainsString('Sisa kesempatan login: 4 kali', session('errors')->first('email'));
        $this->assertNull(session('lockout_seconds'));
    }
}
