<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\TimezoneHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTimezoneLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_timezone_helper_labels_correctly_for_indonesian_timezones(): void
    {
        $this->assertEquals('WIB', TimezoneHelper::getTimezoneLabel('Asia/Jakarta'));
        $this->assertEquals('WIB', TimezoneHelper::getTimezoneLabel('Asia/Pontianak'));
        $this->assertEquals('WITA', TimezoneHelper::getTimezoneLabel('Asia/Makassar'));
        $this->assertEquals('WITA', TimezoneHelper::getTimezoneLabel('Asia/Denpasar'));
        $this->assertEquals('WIT', TimezoneHelper::getTimezoneLabel('Asia/Jayapura'));
    }

    public function test_dynamic_date_and_time_conversion_across_wib_wita_wit(): void
    {
        // 23:30 UTC on Oct 11 -> crosses midnight into Oct 12 in all Indonesian timezones
        $utcTime = Carbon::create(2026, 10, 11, 23, 30, 0, 'UTC');

        // WIB (UTC+7) -> 2026-10-12 06:30:00 WIB
        $wibFormatted = TimezoneHelper::format($utcTime, 'd/m/Y H:i:s', true, 'Asia/Jakarta');
        $this->assertEquals('12/10/2026 06:30:00 WIB', $wibFormatted);

        // WITA (UTC+8) -> 2026-10-12 07:30:00 WITA
        $witaFormatted = TimezoneHelper::format($utcTime, 'd/m/Y H:i:s', true, 'Asia/Makassar');
        $this->assertEquals('12/10/2026 07:30:00 WITA', $witaFormatted);

        // WIT (UTC+9) -> 2026-10-12 08:30:00 WIT
        $witFormatted = TimezoneHelper::format($utcTime, 'd/m/Y H:i:s', true, 'Asia/Jayapura');
        $this->assertEquals('12/10/2026 08:30:00 WIT', $witFormatted);
    }

    public function test_invalid_timezone_falls_back_to_wib_safely(): void
    {
        $this->assertFalse(TimezoneHelper::isValidTimezone('../invalid/path'));
        $this->assertFalse(TimezoneHelper::isValidTimezone('Malicious<script>'));

        $response = $this->withUnencryptedCookie('user_timezone', 'Invalid/Timezone')
            ->get(route('login'));

        $response->assertOk();
        $this->assertEquals('Asia/Jakarta', config('app.user_timezone'));
    }

    public function test_log_history_renders_wita_when_user_is_in_wita_timezone(): void
    {
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole($roleSuperAdmin);

        ActivityLog::create([
            'user_id'     => $superAdmin->id,
            'user_name'   => $superAdmin->name,
            'user_role'   => 'Super Admin',
            'action'      => 'LOGIN',
            'module'      => 'Autentikasi',
            'description' => 'Test log activity WITA',
            'ip_address'  => '127.0.0.1',
            'created_at'  => Carbon::create(2026, 10, 12, 4, 0, 0, 'UTC'),
        ]);

        // Request with WITA cookie (Asia/Makassar)
        $response = $this->actingAs($superAdmin)
            ->withUnencryptedCookie('user_timezone', 'Asia/Makassar')
            ->get(route('superadmin.log-history'));

        $response->assertOk();
        // Topbar badge should display WITA
        $response->assertSee('WITA');
        // The activity log record should display WITA timestamp
        $response->assertSee('Test log activity WITA');
    }

    public function test_log_history_renders_wit_when_user_is_in_wit_timezone(): void
    {
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole($roleSuperAdmin);

        ActivityLog::create([
            'user_id'     => $superAdmin->id,
            'user_name'   => $superAdmin->name,
            'user_role'   => 'Super Admin',
            'action'      => 'LOGIN',
            'module'      => 'Autentikasi',
            'description' => 'Test log activity WIT',
            'ip_address'  => '127.0.0.1',
        ]);

        // Request with WIT cookie (Asia/Jayapura)
        $response = $this->actingAs($superAdmin)
            ->withUnencryptedCookie('user_timezone', 'Asia/Jayapura')
            ->get(route('superadmin.log-history'));

        $response->assertOk();
        // Topbar badge should display WIT
        $response->assertSee('WIT');
        // The activity log record should display WIT timestamp
        $response->assertSee('Test log activity WIT');
    }
}
