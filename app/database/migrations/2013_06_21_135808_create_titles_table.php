<?php

use Illuminate\Database\Migrations\Migration;

class CreateTitlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('titles', function($table)
		{
			$table->increments('id');
			$table->integer('track_id')->unsigned();
			$table->string('title');

			$table->timestamps();

			$table->foreign('track_id')->references('id')->on('tracks');
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
		Schema::drop('titles');
		//
	}

}
