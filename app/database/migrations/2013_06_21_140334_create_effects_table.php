<?php

use Illuminate\Database\Migrations\Migration;

class CreateEffectsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('effects', function($table)
		{
			$table->increments('id');
			$table->integer('track_id')->unsigned();
			$table->enum('difficulty', array('novice', 'advanced', 'exhaust', 'infinite'));
			$table->smallinteger('level')->default(-1);
			$table->string('effected_by');
			$table->string('illustrated_by');
			$table->boolean('available');
			$table->smallinteger('max_chains')->default(-1);

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
		Schema::drop('effects');
		//
	}

}
