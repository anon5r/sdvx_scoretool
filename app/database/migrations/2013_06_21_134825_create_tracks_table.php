<?php

use Illuminate\Database\Migrations\Migration;

class CreateTracksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tracks', function($table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('artist');
			$table->boolean('available');

			$table->timestamps();
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
		Schema::drop('tracks');
		//
	}

}
