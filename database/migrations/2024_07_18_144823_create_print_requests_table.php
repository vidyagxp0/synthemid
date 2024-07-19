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
        Schema::create('print_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('originator_id')->nullable();
            $table->integer('division_id')->nullable();
            $table->longtext('short_description')->nullable();
            $table->longtext('reference_records')->nullable();
            $table->string('due_date')->nullable();
            $table->longtext('permission_user_id')->nullable();
            $table->longtext('hods')->nullable();
            $table->longtext('qa')->nullable();
            $table->string('initial_attachments')->nullable();
            $table->unsignedBigInteger('initiated_by')->nullable();
            $table->text('initiated_on')->nullable();
            $table->text('initiated_comment')->nullable();
            // HOD fields
            $table->text('hod_remarks')->nullable();
            $table->string('hod_attachments')->nullable();
            $table->unsignedBigInteger('hod_by')->nullable();
            $table->text('hod_on')->nullable();
            $table->string('hod_sig_by')->nullable();
            $table->text('hod_sig_on')->nullable();
            $table->text('hod_comment')->nullable();

            // QA fields
            $table->text('qa_remarks')->nullable();
            $table->string('qa_attachments')->nullable();
            $table->unsignedBigInteger('qa_by')->nullable();
            $table->text('qa_on')->nullable();
            $table->string('qa_sig_by')->nullable();
            $table->text('qa_sig_on')->nullable();
            $table->text('qa_comment')->nullable();

            $table->unsignedBigInteger('reject_by')->nullable();
            $table->text('reject_on')->nullable();
            $table->text('reject_comment')->nullable();

            $table->integer('stage')->default(1);
            $table->string('status');
            $table->softDeletes();
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
        Schema::dropIfExists('print_requests');
    }
};
