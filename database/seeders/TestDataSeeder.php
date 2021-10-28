<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Desk;
use App\Models\Locker;
use App\Models\People;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::factory()->create([
            'username' => 'admin',
            'password' => bcrypt('secret'),
        ]);

        $buildings = Building::factory()->count(3)->create();
        $buildings->each(function ($building) {
            $rooms = Room::factory()->count(rand(3, 10))->create(['building_id' => $building->id]);
            $rooms->each(function ($room) {
                $lockers = Locker::factory()->count(rand(30, 100))->create(['room_id' => $room->id]);
                $desks = Desk::factory()->count(rand(30, 100))->create(['room_id' => $room->id]);
            });
        });
        $people = People::factory()->count(rand(300, 500))->create();
        $people->each(function ($person) {
            Desk::inRandomOrder()->take(rand(1, 2))->update(['people_id' => $person->id]);
            Locker::inRandomOrder()->take(rand(1, 2))->update(['people_id' => $person->id]);
        });
        $supervisors = People::factory()->count(rand(5, 10))->create();
        $otherPeople = People::whereNotIn('id', $supervisors->pluck('id'))->get()->each(function ($person) use ($supervisors) {
            $person->supervisor_id = $supervisors->random()->id;
        });
    }
}
