<?php namespace Filta\Services\Queue;

	use Twitter;
	use Queue;
	use User;
	use Log;

	class FollowingQueue {

		public function storeUsers($job, $data)
		{
			$userId = array_get($data, 'user_id');
			$cursor = array_get($data, 'next_cursor');

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
   	 			$friends = Twitter::friendsList($user->twitter_user_id, NULL, $cursor);
   	 		}
   	 		catch(Exception $e)
   	 		{
   	 			Log::error($e);
   	 			$retry = true;
   	 		}
   	 		
   	 		if(!isset($friends['users']))
   	 		{
   	 			Log::error(json_encode($friends));
				$retry = true; 
   	 		}

   	 		if($retry)
   	 		{
   	 			if($job->attempts() < 3)
	 			{
	 				$job->release(120);
	 			}

	 			$job->delete();
	 			exit;
	 		}

   	 		$nextCursor = $friends['next_cursor_str'];
		    $friendIDs = array();

		    foreach($friends['users'] as $friend)
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
		    	$user->following()->sync($syncFriends, false);
		    }

		    $lastImport = ($nextCursor > 0) ? $cursor : 0;

		    $user->last_import = $lastImport;
		    $user->save();

			// Next batch
			if($nextCursor > 0)
			{
				$queueData = array(
					'user_id' => $user->id,
					'next_cursor' => $nextCursor,
				);

				$date = \Carbon\Carbon::now()->addSeconds(120);
				Queue::later($date, '\Filta\Services\Queue\FollowingQueue@storeUsers', $queueData);
			}
			
			$job->delete();
		}
	}