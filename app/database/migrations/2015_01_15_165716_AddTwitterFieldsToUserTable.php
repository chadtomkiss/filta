<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTwitterFieldsToUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table) {
			$table->string('twitter_user_id')->nullable();
			$table->index('twitter_user_id');

			$table->string('twitter_user_name')->nullable();
			$table->string('twitter_user_screen_name')->nullable();
			$table->string('twitter_user_description')->nullable();
			$table->string('twitter_user_location')->nullable();
			$table->string('twitter_user_url')->nullable();
			$table->string('twitter_user_profile_image_url')->nullable();
			$table->string('twitter_user_profile_image_url_https')->nullable();
			$table->string('twitter_oauth_token')->nullable();
			$table->string('twitter_oauth_token_secret')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table) {

			$table->dropIndex('users_twitter_user_id_index');

			$table->dropColumn('twitter_user_id');
			$table->dropColumn('twitter_user_name');
			$table->dropColumn('twitter_user_screen_name');
			$table->dropColumn('twitter_user_description');
			$table->dropColumn('twitter_user_location');
			$table->dropColumn('twitter_user_url');
			$table->dropColumn('twitter_user_profile_image_url');
			$table->dropColumn('twitter_user_profile_image_url_https');
			$table->dropColumn('twitter_oauth_token');
			$table->dropColumn('twitter_oauth_token_secret');
		});
	}

}
