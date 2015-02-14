<?php namespace Dashboard;

	use BaseController;
	use Sentry;
	use View;
	use User;
	use Twitter;
	use Log;
	use Redirect;
	use Queue;
	use Input;

	class ImportController extends BaseController {

		public function getImport()
		{
			return View::make('dashboard.import');
		}
		
		public function postImport()
		{
			$user = Sentry::getUser();

	    	$twitterOAuthToken = $user->twitter_oauth_token;
	    	$twitterOAuthSecret = $user->twitter_oauth_token_secret;

	    	Twitter::setOAuthToken($twitterOAuthToken);
	        Twitter::setOAuthTokenSecret($twitterOAuthSecret);

	        $friends = NULL;
	        try
	        {
	            $friends = Twitter::friendsIds($user->twitter_user_id);
	        }
	        catch(Exception $e)
	        {
	            Log::error($e);

	            return Redirect::to('dashboard');
	        }

	        if(!isset($friends['ids']))
	        {
	            Log::error(var_export($friends, TRUE));

	            return Redirect::to('dashboard');
	        }

	        Log::info('# of users to import: '.count($friends['ids']));

	        $processNow = array_slice($friends['ids'], 0, 200);
	        $processLater = array_slice($friends['ids'], 200);

	        $friendIDs = array();
	        foreach(array_chunk($processNow, 100) as $twitterIDs) 
	        {
	            $friendArray = Twitter::usersLookup($twitterIDs);

	            foreach($friendArray as $friend)
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
	        }

	        Log::info('adding for {$user->id}: '.count($friendIDs));

            $twitterUserIDs = array_column($friendIDs, 'twitter_user_id');
            $existingUsers = User::whereIn('twitter_user_id', $twitterUserIDs)->lists('twitter_user_id', 'id');

            $createFriends = array_filter($friendIDs, function($row) use($existingUsers) {
		        return (!in_array($row['twitter_user_id'], $existingUsers));
		    });

            $syncFriends = array_keys($existingUsers);

            Log::info("users already in db: ".count($existingUsers));

            if($createFriends)
            {
            	$insertFriendIDs = array_column($createFriends, 'twitter_user_id');
            	$insertFriends = array_column($createFriends, 'details');

                User::insert($insertFriends);
                $newUsers = User::whereIn('twitter_user_id', $insertFriendIDs)->lists('id');

                Log::info("creating users for {$user->id}: ".count($newUsers));

                $syncFriends = $syncFriends + $newUsers;
            }

            if($syncFriends)
            {
            	Log::info("sync friends for {$user->id}: ".count($syncFriends));

                $existing = $user->following()->whereIn('following_id', $syncFriends)->lists('following_id');

                Log::info("existing friends for {$user->id}: ".count($existing));

                $createFriends = ($existing) ? array_diff($syncFriends, $existing) : $syncFriends;

                if($createFriends)
                {
                	Log::info("attaching friends for {$user->id}: ".count($createFriends));

                    $user->following()->attach($createFriends);
                }
            }

	        if($processLater)
	        {
            	Log::info("# friends to queue for {$user->id}: ".count($processLater));

	            $date = \Carbon\Carbon::now();
	            foreach(array_chunk($processLater, 100) as $friendIds)
	            {
	                $queueData = array(
	                    'user_id' => $user->id,
	                    'friend_ids' => $friendIds,
	                );

	                $queueDate = $date->addSeconds(30);
	                Queue::later($queueDate, '\Filta\Services\Queue\FollowingQueue@storeUsers', $queueData);
	            }
	        }

	        return Redirect::to('dashboard');
		}
	}