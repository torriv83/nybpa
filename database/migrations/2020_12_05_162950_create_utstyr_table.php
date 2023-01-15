<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtstyrTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utstyr', function (Blueprint $table) {
            $table->id();
            $table->string('hva', 191);
            $table->string('navn', 191);
            $table->string('artikkelnummer', 191)->nullable();
            $table->string('antall', 191);
            $table->string('link', 191)->nullable();
            $table->integer('kategoriID')->unsigned()->index('utstyr_kategoriid_foreign');
            $table->softDeletes();
            // $table->foreign('kategoriID')->references('id')->on('kategori')->onUpdate('RESTRICT')->onDelete('RESTRICT');
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
        Schema::dropIfExists('utstyr');
    }
}
