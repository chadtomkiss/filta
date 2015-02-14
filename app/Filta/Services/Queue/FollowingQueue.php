<?php namespace Filta\Services\Queue;

	use DB;
	use Twitter;
	use Queue;
	use User;
	use Log;

	class FollowingQueue {

		public function storeUsers($job, $data)
		{
			DB::reconnect();

			$userId = array_get($data, 'user_id');
			$addFriends = array_get($data, 'friend_ids');
			$user = User::find($userId);

			if(!$user)
			{
				exit;
			}

			$twitterOAuthToken = $user->twitter_oauth_token;
			$twitterOAuthSecret = $user->twitter_oauth_token_secret;

			if(!$twitterOAuthToken || !$twitterOAuthSecret)
			{
				Log::error('No token for UserID: ' . $user->id);
				exit;
			}

			Twitter::setOAuthToken($twitterOAuthToken);
   	 		Twitter::setOAuthTokenSecret($twitterOAuthSecret);

   	 		$friends = array();
   	 		$retry = false;
   	 		try
   	 		{
   	 			$friends = Twitter::usersLookup($addFriends);
   	 		}
   	 		catch(Exception $e)
   	 		{
   	 			Log::error($e);
   	 		}

            Log::info("queue import for {$user->id}: ".count($addFriends));

	 		if($job->attempts() > 3)
 			{
 				Log::info('deleting job for {$user->id}');
 				$job->delete();
 			}

		    $friendIDs = array();

		    foreach($friends as $friend)
		    {
		    	$twitter_user_id = array_get($friend, 'id_str');
			    $twitter_user_name = array_get($friend, 'name');
			    $twitter_user_screen_name = array_get($friend, 'screen_name');
			    $twitter_user_location = array_get($friend, 'location');
			    $twitter_user_description = array_get($friend, 'description');
			    $twitter_user_url = array_get($friend, 'entities.url.urls.0.expanded_url');
			    $twitter_user_profile_image_url = array_get($friend, 'profile_image_url');
			    $twitter_user_profile_image_url_https = array_get($friend, 'profile_image_url_https');

			    $email = $twitter_user_id."@twitter";
		    	$password = $email.time();
		    	
			    $details = array(
			    	'email' => $email,
			    	'password' => $password,
			    	'twitter_user_id' => $twitter_user_id,
			    	'twitter_user_name' => $twitter_user_name,
			    	'twitter_user_screen_name' => $twitter_user_screen_name,
			    	'twitter_user_location' => $twitter_user_location,
			    	'twitter_user_description' => $twitter_user_description,
			    	'twitter_user_url' => $twitter_user_url,
			    	'twitter_user_profile_image_url' => $twitter_user_profile_image_url,
			    	'twitter_user_profile_image_url_https' => $twitter_user_profile_image_url_https,
			    	'activated' => true,
			    	'created_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s'),
           	 		'updated_at' => \Carbon\Carbon::now()->format('Y-m-d H:i:s')
			    );

			    $friendIDs[] = array(
                	'twitter_user_id' => $twitter_user_id,
                	'details' => $details
                );
		    }

			Log::info('queue: adding for {$user->id}: '.count($friendIDs));

            $twitterUserIDs = array_column($friendIDs, 'twitter_user_id');
            $existingUsers = User::whereIn('twitter_user_id', $twitterUserIDs)->lists('twitter_user_id', 'id');

            $createFriends = array_filter($friendIDs, function($row) use($existingUsers) {
		        return (!in_array($row['twitter_user_id'], $existingUsers));
		    });

            $syncFriends = array_keys($existingUsers);

            Log::info("queue: users already in db: ".count($existingUsers));

            if($createFriends)
            {
            	$insertFriendIDs = array_column($createFriends, 'twitter_user_id');
            	$insertFriends = array_column($createFriends, 'details');

                User::insert($insertFriends);
                $newUsers = User::whereIn('twitter_user_id', $insertFriendIDs)->lists('id');

                Log::info("queue: creating users for {$user->id}: ".count($newUsers));

                $syncFriends = $syncFriends + $newUsers;
            }

            if($syncFriends)
            {
            	Log::info("queue: sync friends for {$user->id}: ".count($syncFriends));

                $existing = $user->following()->whereIn('following_id', $syncFriends)->lists('following_id');

                Log::info("queue: existing friends for {$user->id}: ".count($existing));

                $createFriends = ($existing) ? array_diff($syncFriends, $existing) : $syncFriends;

                if($createFriends)
                {
                	Log::info("queue: attaching friends for {$user->id}: ".count($createFriends));

                    $user->following()->attach($createFriends);
                }
            }

			$job->delete();
		}
	}