<?php

// Migration to add amount_paid field to orders table

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountPaidToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0);  // Adds amount_paid field, with a default value of 0
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
}
