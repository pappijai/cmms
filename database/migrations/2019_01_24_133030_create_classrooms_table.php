<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->increments('ClassroomID');
            $table->string('ClassroomCode');
            $table->string('ClassroomName');
            $table->integer('ClassroomType');
            $table->time('ClassroomIn');
            $table->time('ClassroomOut');
            $table->integer('ClassroomBldg');
            $table->integer('ClassroomFloor');
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
        Schema::dropIfExists('classrooms');
    }
}
