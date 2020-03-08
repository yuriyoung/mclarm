<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique(); // Represents a username
            $table->string('email')->unique();
            $table->string('password')->nullable();;
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // accounts
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->string('provider_name');
            $table->string('provider_id')->nullable();
            $table->string('access_token')->nullable();;
            $table->string('refresh_token')->nullable();
            $table->timestamp('expires_in')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('nickname')->nullable();
            $table->string('avatar')->nullable();
            $table->string('qrcode')->nullable();
            $table->string('gravatar')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('location')->nullable();
            $table->string('company')->nullable();
            $table->enum('gender', ['male', 'female', 'neuter']); // 0-male, 1-female, 2-neuter
            $table->string('birthday')->nullable();
            $table->string('career')->nullable();
            $table->string('website')->nullable();
            $table->string('github')->nullable();
            $table->string('address_home')->nullable();
            $table->string('address_work')->nullable();
            $table->string('bio')->nullable();;
            $table->longText('about')->nullable();;
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('banned_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->longText('reason');
            $table->unsignedInteger('days')->default(7);
            $table->timestamp('banned_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_signed_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('device')->nullable();
            $table->string('platform')->nullable();
            $table->string('client')->nullable();
            $table->string('ip')->nullable();
            $table->timestamp('signed_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_signed_logs');
        Schema::dropIfExists('banned_users');
        Schema::dropIfExists('user_details');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('users');
    }
}
