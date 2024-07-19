<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\QMSDivision;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $division = new QMSDivision();
        $division->name = "Corporate";
        $division->status = 1;
        $division->save();

        $division = new QMSDivision();
        $division->name = "Plant";
        $division->status = 1;
        $division->save();
    }
}
