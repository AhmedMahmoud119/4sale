<?php
namespace Tests\Unit;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Table;
use App\Models\Reservation;
use App\Models\Meal;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Strategies\TaxAndServiceStrategy;
use App\Strategies\ServiceOnlyStrategy;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
        $this->seed();
    }

    public function testPayWithTaxAndServiceStrategy()
    {
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table->id,
            'customer_id' => $customer->id,
        ]);
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => $reservation->id,
            'customer_id' => $customer->id,
            'total' => 100.00,
            'paid' => false,
        ]);
        $meal = Meal::factory()->create();
        OrderDetail::factory()->create([
            'order_id' => $order->id,
            'meal_id' => $meal->id,
            'amount_to_pay' => 100.00,
        ]);

        $this->app->bind(CheckoutStrategy::class, TaxAndServiceStrategy::class);

        $data = ['order_id' => $order->id];

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/pay', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'invoice']);
    }

    public function testPayWithServiceOnlyStrategy()
    {
        $customer = Customer::factory()->create();
        $table = Table::factory()->create();
        $reservation = Reservation::factory()->create([
            'table_id' => $table->id,
            'customer_id' => $customer->id,
        ]);
        $order = Order::factory()->create([
            'table_id' => $table->id,
            'reservation_id' => $reservation->id,
            'customer_id' => $customer->id,
            'total' => 100.00,
            'paid' => false,
        ]);
        $meal = Meal::factory()->create();
        OrderDetail::factory()->create([
            'order_id' => $order->id,
            'meal_id' => $meal->id,
            'amount_to_pay' => 100.00,
        ]);

        $this->app->bind(CheckoutStrategy::class, ServiceOnlyStrategy::class);

        $data = ['order_id' => $order->id];

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'sanctum')->postJson('/api/pay', $data);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'invoice']);
    }
}
