<?php

use Illuminate\Database\Migrations\Migration;

class CreatePlayDatasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('play_datas', function($table)
		{
			$table->increments('id');
			$table->integer('effect_id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->enum('medal', array('crash', 'comp', 'uc', 'per'));
			$table->enum('grade', array('d', 'c', 'b', 'a', 'aa', 'aaa'));
			$table->integer('highscore')->default(-1);
			$table->smallinteger('play_count')->default(-1);
			$table->smallinteger('comp_count')->default(-1);
			$table->smallinteger('uc_count')->default(-1);
			$table->smallinteger('per_count')->default(-1);

			$table->timestamps();

			$table->foreign('effect_id')->references('id')->on('effects');
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
		Schema::drop('play_datas');
		//
	}

}

