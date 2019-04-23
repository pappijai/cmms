<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectTaggingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subject_taggings', function (Blueprint $table) {
            $table->increments('STID');
            $table->integer('SubjectID');
            $table->integer('ProfessorID');
            $table->integer('SectionID');
            $table->integer('STUnits');
            $table->string('STSem');
            $table->integer('STYear');
            $table->year('STYearFrom');
            $table->year('STYearTo');
            $table->string('STStatus');
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
        Schema::dropIfExists('subject_taggings');
    }
}
