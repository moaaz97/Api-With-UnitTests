<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourListTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_return_list_of_tours_by_travel_slug(): void
    {
        $travels = Travel::factory()->create();

        $tour = Tour::factory()->create(['travel_id' => $travels->id]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment(['id' => $tour->id]);
    }

    public function test_tours_price_is_shown_correctly(): void
    {
        $travels = Travel::factory()->create();

        Tour::factory()->create([
            'travel_id' => $travels->id,
            'price' => 125.65,
        ]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment(['price' => '125.65']);
    }

    public function test_tours_return_list_pagination_correctly(): void
    {
        $travels = Travel::factory()->create();

        Tour::factory(16)->create(['travel_id' => $travels->id]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.last_page', 2);
    }
}
