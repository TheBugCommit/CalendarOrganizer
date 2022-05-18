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
            $table->id();
            $table->string('name', 30);
            $table->string('email', 100)->unique();
            $table->string('password', 60);
            $table->string('surname1', 30);
            $table->string('surname2', 30);
            $table->boolean('locked')->default(false);
            $table->date('birth_date');
            $table->string('phone', 15)->nullable();
            $table->enum('gender', ['M', 'F' ,'O']);
            $table->foreignId('nation_id')->constrained('nations')->restrictOnDelete();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
