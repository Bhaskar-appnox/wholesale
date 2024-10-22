<?php

// Migration to modify payments table
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Check if 'user_id' exists, if not, add it.
            if (!Schema::hasColumn('payments', 'user_id')) {
                $table->bigInteger('user_id')->unsigned()->nullable();
            }

            // Add foreign key if it doesn't exist
            if (!Schema::hasForeignKey('payments_user_id_foreign')) {
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            }
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['user_id']);

            // Drop column 'user_id'
            $table->dropColumn('user_id');
        });
    }
}

