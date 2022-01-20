<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoldersTable extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->boolean('isFolder');
            $table->integer('parent');
            $table->string('path');
            $table->unsignedBigInteger('cabinet_id')->nullable();
            $table->foreign('cabinet_id')->references('id')->on('cabinets')->onDelete('cascade');
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
        Schema::dropIfExists('folders');
    }
}
