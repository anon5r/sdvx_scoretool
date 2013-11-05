<?php

use Illuminate\Database\Migrations\Migration;

class CreateProfilesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profiles', function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('name', 16);
			$table->string('skill', 8);
			$table->smallinteger('play_count');
			$table->integer('packet');
			$table->integer('block');

			$table->timestamps();	

			$table->foreign('user_id')->references('id')->on('users');
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
		Schema::drop('profiles');
		//
	}

}
