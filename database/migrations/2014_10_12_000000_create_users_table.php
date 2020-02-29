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
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('avatar')->nullable()->comment('头像');
            $table->string('qrcode')->nullable()->comment('二维码');
            $table->string('first_name')->nullable()->comment('名');
            $table->string('last_name')->nullable()->comment('姓');
            $table->enum('gender', ['male', 'female', 'neuter'])->comment('性别'); // 0-male, 1-female, 2-neuter
            $table->string('birthday')->nullable()->comment('生日');
            $table->string('career')->nullable()->comment('职业');
            $table->string('website')->nullable()->comment('主页');
            $table->string('github')->nullable()->comment('GitHub');
            $table->string('address_home')->nullable();
            $table->string('address_work')->nullable();
            $table->string('signature')->nullable()->comment('签名');;
            $table->longText('about')->nullable()->comment('关于我');;
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('banned_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->longText('reason')->comment('原因');
            $table->unsignedInteger('days')->default(7);
            $table->timestamp('banned_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_signed_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('device')->nullable()->comment('登录设备');
            $table->string('platform')->nullable()->comment('登录平台');
            $table->string('client')->nullable()->comment('登录客户端');
            $table->string('ip')->nullable()->comment('登录ip');
            $table->timestamp('signed_at')->useCurrent()->comment('登录日期');

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
        Schema::dropIfExists('users');
    }
}
