<?php

namespace Tests\Unit;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Meal;

class OrderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->seed();
    }

    public function testPlaceOrder()
    {
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table->id,
            'customer_id' => $customer->id,
        ]);
        $meal1 = Meal::factory()->create();
        $meal2 = Meal::factory()->create();

        $user = User::factory()->create();
        $data = [
            'user_id' => $table->id,
            'customer_id' => $customer->id,
            'table_id' => $table->id,
            'reservation_id' => $reservation->id,
            'meals' => [
                ['id' => $meal1->id, 'quantity' => 2],
                ['id' => $meal2->id, 'quantity' => 1]
            ]
        ];

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/order', $data);

        $response->assertStatus(201);
        $response->assertJsonStructure(['order', 'order_details']);
    }
}
