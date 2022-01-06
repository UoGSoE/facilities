<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Desk;
use App\Models\User;
use App\Models\Locker;
use App\Models\People;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeskLockerAllocatedAtTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function when_a_desk_or_locker_is_allocated_or_unallocated_the_allocated_at_field_is_set_appropriately()
    {
        $person = People::factory()->create();
        $desk = Desk::factory()->create();
        $locker = Locker::factory()->create();

        $this->assertNull($desk->allocated_at);
        $this->assertNull($locker->allocated_at);

        $desk->allocateTo($person);
        $locker->allocateTo($person);

        $this->assertEquals(now()->format('Y-m-d'), $desk->fresh()->allocated_at->format('Y-m-d'));
        $this->assertEquals(now()->format('Y-m-d'), $locker->fresh()->allocated_at->format('Y-m-d'));

        $desk->deallocate();
        $locker->deallocate();

        $this->assertNull($desk->fresh()->allocated_at);
        $this->assertNull($locker->fresh()->allocated_at);
    }
}
