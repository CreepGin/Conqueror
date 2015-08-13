<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nodes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('project');
            $table->string('name');
            $table->text('body')->nullable();
            $table->float('completion')->default(0.0);
            $table->float('weight')->default(1.0);
            $table->json('options')->nullable();
            $table->integer('parent_id')->unsigned()->default(0);
            //$table->foreign('parent_id')->references('id')->on('nodes');
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
        Schema::drop('nodes');
    }
}
