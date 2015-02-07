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
			$friendIds = array_get($data, 'friend_ids');
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

   	 		$friends = NULL;
   	 		$retry = false;
   	 		try
   	 		{
   	 			$friends = Twitter::usersLookup($friendIds);
   	 		}
   	 		catch(Exception $e)
   	 		{
   	 			Log::error($e);
   	 		}

	 		if($job->attempts() > 3)
 			{
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

			    $friendIDs[$twitter_user_id] = $details;
		    }

			$twitterUserIDs = array_keys($friendIDs);
			$existingUsers = User::whereIn('twitter_user_id', $twitterUserIDs)->lists('twitter_user_id', 'id');

		    $createFriends = array_diff_key($friendIDs, array_flip(array_values($existingUsers)));
		    $syncFriends = array_keys($existingUsers);

		    if($createFriends)
		    {
		    	User::insert($createFriends);
		    	$newUsers = User::whereIn('twitter_user_id', array_keys($createFriends))->lists('id');

		    	$syncFriends = $syncFriends + $newUsers;
		    }

		    if($syncFriends)
		    {
		    	$existing = $user->following()->whereIn('following_id', $syncFriends)->lists('following_id');

		        $createFriends = ($existing) ? array_diff($syncFriends, $existing) : $syncFriends;

		        if($createFriends)
		        {
		            $user->following()->attach($createFriends);
		        }
		    }

			$job->delete();
		}
	}