<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCourseSubjectOfferedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_subject_offereds', function (Blueprint $table) {
            $table->increments('CSOID');
            $table->integer('SubjectID');
            $table->integer('CourseID');
            $table->integer('CSOYear');
            $table->string('CSOSem');
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
        Schema::dropIfExists('course_subject_offereds');
    }
}
