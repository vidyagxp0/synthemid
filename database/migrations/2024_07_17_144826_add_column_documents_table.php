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
        Schema::table('documents', function (Blueprint $table) {

            $table->string('initial_attachments')->nullable();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->text('initiated_on')->nullable();

            // Drafter fields
            $table->text('drafter_remarks')->nullable();
            $table->string('drafter_attachments')->nullable();
            $table->unsignedBigInteger('drafted_by')->nullable();
            $table->text('drafted_on')->nullable();

            // HOD fields
            $table->text('hod_remarks')->nullable();
            $table->string('hod_attachments')->nullable();
            $table->unsignedBigInteger('hod_by')->nullable();
            $table->text('hod_on')->nullable();

            // QA fields
            $table->text('qa_remarks')->nullable();
            $table->string('qa_attachments')->nullable();
            $table->unsignedBigInteger('qa_by')->nullable();
            $table->text('qa_on')->nullable();

            // Reviewer fields
            $table->text('reviewer_remarks')->nullable();
            $table->string('reviewer_attachments')->nullable();
            $table->unsignedBigInteger('reviewer_by')->nullable();
            $table->text('reviewer_on')->nullable();

            // Approver fields
            $table->text('approver_remarks')->nullable();
            $table->string('approver_attachments')->nullable();
            $table->unsignedBigInteger('approver_by')->nullable();
            $table->text('approver_on')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
