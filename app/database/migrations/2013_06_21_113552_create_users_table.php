<?php

use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function($table)
		{
			$table->increments('id');
			$table->string('name', 16);
			$table->string('hashed_password', 64);
			//$table->text('privates');
			$table->string('hashed_last_profile', 32);
			$table->boolean('available');
			$table->boolean('show_play_count');
			$table->timestamps();

			$table->unique('name');
		});
		//
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users');
		//
	}

}
