<?php

namespace Database\Seeders;
use App\Models\Stage;
use App\Models\RecordNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $stage = new Stage();
        $stage->name = "Initiate";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Draft";
        $stage->save();

        $stage = new Stage();
        $stage->name = "HOD/CFT Review";
        $stage->save();

        $stage = new Stage();
        $stage->name = "QA Review";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Reviewer Review";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Approver Pending";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Under-Training";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Training-Complete";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Effective";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Obsolete";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Cancel-by-Drafter";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Cancel-by-HOD";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Cancel-by-QA";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Cancel-by-Reviewer";
        $stage->save();

        $stage = new Stage();
        $stage->name = "Cancel-by-Approver";
        $stage->save();

        $stage = new RecordNumber();
        $stage->counter = 0;
        $stage->save();
    }
}
