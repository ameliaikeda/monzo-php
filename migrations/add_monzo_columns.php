<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMonzoColumns extends Migration
{
    /**
     * Run migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('monzo_user_id')->index();
            $table->text('monzo_access_token');
            $table->text('monzo_refresh_token');
        });
    }

    /**
     * Reverse migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'monzo_user_id',
                'monzo_access_token',
                'monzo_refresh_token',
            ]);
        });
    }
}
