<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    public function run()
    {
        Topic::factory()->count(100)->create();
    }
}
