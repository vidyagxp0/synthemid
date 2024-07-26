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
        Schema::create('print_pdf_histories', function (Blueprint $table) {
            $table->id();
            $table->text('document_name');
            $table->integer('issue_copies');
            $table->text('print_reason');
            $table->text('printed_by');
            $table->text('printed_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('print_pdf_histories');
    }
};
