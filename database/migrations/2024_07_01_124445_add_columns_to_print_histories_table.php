<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->string('issue_copies')->default(0)->nullable();
            $table->longText('print_reason')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('print_histories', function (Blueprint $table) {
            $table->dropColumn('issue_copies');
            $table->dropColumn('print_reason');
        });
    }
};
