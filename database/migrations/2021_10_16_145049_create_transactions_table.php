<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('source_account')->nullable(false);
            $table->string('destination_account')->nullable(false);
            $table->string('operation_type')->nullable(false);
            $table->decimal('amount')->nullable(false);
            $table->string('concept')->nullable(false);
            $table->string('reference')->nullable(false);
            $table->dateTime('transaction_date')->nullable(false);
            $table->string('status')->nullable(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('source_account')->references('account_number')->on('accounts');
            $table->foreign('destination_account')->references('account_number')->on('accounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transactions');
    }
}
