<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectTaggingSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_tagging_schedules', function (Blueprint $table) {
            $table->increments('STSID');
            $table->integer('STID');
            $table->integer('ClassroomID');
            $table->integer('STSHours');
            $table->time('STSTimeStart');
            $table->time('STSTimeEnd');
            $table->string('STSDay');
            $table->string('STSStatus');
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
        Schema::dropIfExists('subject_tagging_schedules');
    }
}
