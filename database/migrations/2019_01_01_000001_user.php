<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Squadron\Base\Helpers\Database\DatabaseSchema;

class User extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // password reset
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // user
        DatabaseSchema::create('user', function (Blueprint $table) {
            $table->boolean('active')->default(true);

            $table->string('email', 100)->unique();
            $table->string('firstName', 50)->nullable();
            $table->string('lastName', 50)->nullable();

            $table->enum('role', array_merge(
                ['root', 'user'],
                config('squadron.user.additionalRoles', [])
            ))->default('user');

            $table->string('password');
            $table->string('rememberToken', 100)->nullable();
        }, null, true);

        // user social attaches
        DatabaseSchema::create('user_social', function (Blueprint $table) {
            $table->string('provider');
            $table->string('providerId');
            $table->unique(['provider', 'providerId']);
        }, ['user']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('user_social');
        Schema::drop('user');
        Schema::drop('password_resets');
    }
}
