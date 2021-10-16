<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {

            $table->bigIncrements('id');
            $table->unsignedBigInteger('person_id')->nullable();

            $table->string('account_number')->unique();
            $table->string('product');
            $table->decimal('balance')->nullable(false);
            $table->string('status');
            $table->unsignedInteger('nip')->nullable(false);
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('people');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
