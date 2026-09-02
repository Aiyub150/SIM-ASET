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
            ['reference_code' => 'INVALID/2026/09'],
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
}
