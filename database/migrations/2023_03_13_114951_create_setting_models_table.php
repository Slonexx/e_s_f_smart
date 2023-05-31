<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_models', function (Blueprint $table) {
            $table->string('accountId')->unique()->primary();
            $table->string('tokenMs');

            $table->string('urlOrganization');
            $table->string('AUTH_RSA256');
            $table->string('RSA256');
            $table->string('GOSTKNCA')->nullable();

            $table->string('tin');
            $table->string('Username');
            $table->string('Password');

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
        Schema::dropIfExists('setting_models');
    }
};
