<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('payment_terms', ['prepayment', 'post-payment', 'partial-payment'])->default('prepayment');
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('outstanding_balance', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['payment_terms', 'credit_limit', 'outstanding_balance']);
        });
    }
}
