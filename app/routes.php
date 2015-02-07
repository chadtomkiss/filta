<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@getIndex'));

Route::get('dashboard', array('as' => 'dashboard', 'uses' => 'Dashboard\HomeController@getIndex'));

Route::get('twitter/connect', array('as' => 'twitter.connect', 'uses' => function(){
    // Reqest tokens
    $tokens = Twitter::oAuthRequestToken();

    // Redirect to twitter
    Twitter::oAuthAuthenticate(array_get($tokens, 'oauth_token'));
    exit;
}));

Route::get('twitter/auth', function(){
    // Oauth token
    $token = Input::get('oauth_token');

    // Verifier token
    $verifier = Input::get('oauth_verifier');

    // Request access token
    $accessToken = Twitter::oAuthAccessToken($token, $verifier);

    $twitterUserID = $accessToken['user_id'];
    $twitterOAuthToken = $accessToken['oauth_token'];
    $twitterOAuthSecret = $accessToken['oauth_token_secret'];

    Twitter::setOAuthToken($twitterOAuthToken);
    Twitter::setOAuthTokenSecret($twitterOAuthSecret);

    $twitterUser = Twitter::usersShow($twitterUserID);

    $email = $twitterUserID."@twitter";
    $password = $email.time();
    $twitter_user_id = array_get($twitterUser, 'id_str');
    $twitter_user_name = array_get($twitterUser, 'name');
    $twitter_user_screen_name = array_get($twitterUser, 'screen_name');
    $twitter_user_location = array_get($twitterUser, 'location');
    $twitter_user_description = array_get($twitterUser, 'description');
    $twitter_user_url = array_get($twitterUser, 'entities.url.urls.0.expanded_url');
    $twitter_user_profile_image_url = array_get($twitterUser, 'profile_image_url');
    $twitter_user_profile_image_url_https = array_get($twitterUser, 'profile_image_url_https');

    // Update Tokens
    $twitter_oauth_token = $twitterOAuthToken;
    $twiter_oauth_token_secret = $twitterOAuthSecret;

    $user = User::where('twitter_user_id', $twitter_user_id)->first();

    if(!$user)
    {
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
	    	'twitter_oauth_token' => $twitter_oauth_token,
	    	'twitter_oauth_token_secret' => $twiter_oauth_token_secret,
	    	'activated' => true,
	    );

	    $user = Sentry::createUser($details);
    }
    else
    {
    	$user->twitter_user_id = $twitter_user_id;
	    $user->twitter_user_name = $twitter_user_name;
	    $user->twitter_user_screen_name = $twitter_user_screen_name;
	    $user->twitter_user_location = $twitter_user_location;
	    $user->twitter_user_description = $twitter_user_description;
	    $user->twitter_user_url = $twitter_user_url;
	    $user->twitter_user_profile_image_url = $twitter_user_profile_image_url;
	    $user->twitter_user_profile_image_url_https = $twitter_user_profile_image_url_https;

	    // Update Tokens
	    $user->twitter_oauth_token = $twitter_oauth_token;
	    $user->twitter_oauth_token_secret = $twiter_oauth_token_secret;

	    $user->save();
    }

    Sentry::login($user);

    return Redirect::route('dashboard');
});

Route::get('twitter/import', function() {
	return View::make('dashboard.import');
});

Route::get('logout', array('as' => 'logout', 'uses' => function() {
	Sentry::logout();

	return Redirect::route('home');
}));

Route::post('twitter/import', array('as' => 'twitter.import.post', 'uses' => function() {

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
        return Redirect::to('dashboard');
    }

    $processNow = array_slice($friends['ids'], 0, 200);
    $processLater = array_slice($friends['ids'], 200);

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
    }

    if($processLater)
    {
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
}));

Route::get('following/search', function() {
	$user = Sentry::getUser();
	$userID = $user->id;

	$input = Input::all();

	$term = array_get($input, 'query');

	$followingRepo = new Filta\Repository\FollowingRepository;
	$search = $followingRepo->search($userID, $term);

    $data['users'] = $search;

    $html = View::make('partials.following_table', $data)->render();

	return $html;
});