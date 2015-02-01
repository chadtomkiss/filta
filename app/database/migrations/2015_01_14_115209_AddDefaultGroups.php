<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDefaultGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		// Create the Users group
	    $group = Sentry::createGroup(array(
	        'name'        => 'Users',
	        'permissions' => array(
	            'users' => 1
	        ),
	    ));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Find the group using the group name
	    $group = Sentry::findGroupByName('Users');

	    // Delete the group
	    $group->delete();
	}

}
