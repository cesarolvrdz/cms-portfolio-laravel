<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TechTag;

class TechTagSeeder extends Seeder
{
    public function run()
    {
        TechTag::factory()->count(10)->create();
    }
}
