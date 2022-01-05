<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Desk;
use App\Models\ItAsset;
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

        $buildings = collect([
            'James Watt South',
            'James Watt North',
            'Rankine',
            'St.Andrews',
        ])->map(fn ($name) => Building::factory()->create(['name' => $name]));
        $buildings->each(function ($building) {
            $rooms = Room::factory()->count(rand(3, 10))->create(['building_id' => $building->id]);
            $rooms->each(function ($room) {
                collect(range(1, rand(10, 60)))->each(function ($i) use ($room) {
                    Desk::factory()->create(['room_id' => $room->id, 'name' => "$i"]);
                });
                collect(range(1, rand(5, 20)))->each(function ($i) use ($room) {
                    Locker::factory()->create(['room_id' => $room->id, 'name' => "$i"]);
                });
            });
        });
        $itAssets = ItAsset::factory()->count(rand(100, 200))->create();
        $people = People::factory()->count(rand(300, 500))->create();
        $leftPeople = People::factory()->count(rand(5, 20))->create(['start_at' => now()->subMonths(rand(12, 24)), 'end_at' => now()->subWeeks(rand(2, 8))]);
        People::all()->each(function ($person) {
            Desk::inRandomOrder()->take(rand(1, 2))->update(['people_id' => $person->id]);
            Locker::inRandomOrder()->take(rand(1, 2))->update(['people_id' => $person->id]);
            ItAsset::inRandomOrder()->take(rand(3, 5))->update(['people_id' => $person->id]);
        });
        $pendingPeople = collect(range(1, rand(20, 50)))->map(fn ($count) => People::factory()->pending()->create(['start_at' => now()->addDays(rand(1, 30))]));
        $supervisors = People::factory()->count(rand(10, 20))->create();
        $otherPeople = People::whereNotIn('id', $supervisors->pluck('id')->toArray())->get()->each(function ($person) use ($supervisors) {
            $person->supervisor_id = $supervisors->random()->id;
            $person->save();
        });
    }
}
