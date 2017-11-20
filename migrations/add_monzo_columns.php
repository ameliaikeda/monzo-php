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
            $table->string('monzo_user_id')->index()->nullable();
            $table->text('monzo_access_token')->nullable();
            $table->text('monzo_refresh_token')->nullable();

            $table->string(config('monzo.webhooks.user_token'))->nullable();
            $table->string(config('monzo.webhooks.token'))->nullable();
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
                config('monzo.webhooks.token'),
                config('monzo.webhooks.user_token'),
            ]);
        });
    }
}
