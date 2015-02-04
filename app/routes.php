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
	set_time_limit(0);
    ini_set('max_execution_time', 500);

    $user = Sentry::getUser();

	$twitterOAuthToken = $user->twitter_oauth_token;
	$twitterOAuthSecret = $user->twitter_oauth_token_secret;

	Twitter::setOAuthToken($twitterOAuthToken);
    Twitter::setOAuthTokenSecret($twitterOAuthSecret);

    $friends = NULL;
    try
    {
        $friends = Twitter::friendsList($user->twitter_user_id);
    }
    catch(Exception $e)
    {
        Log::error($e);
        dd($e);
        exit;
    }

    if(!isset($friends['users']))
    {
        dd($friends);
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

    // Nope..
    // Instead, search twitter_user for $syncFriends
    // Diff result with $syncFriends
    if($syncFriends)
    {
    	$user->following()->sync($syncFriends, true);
    }

    $queueData = array(
        'user_id' => $user->id,
        'next_cursor' => $nextCursor,
    );

    $date = \Carbon\Carbon::now()->addSeconds(90);
    Queue::later($date, '\Filta\Services\Queue\FollowingQueue@storeUsers', $queueData);

    return Redirect::to('dashboard');
}));

Route::get('api/following', function() {
	$user = Sentry::getUser();
	$userID = $user->id;

	$input = Input::all();

	$term = array_get($input, 'query');

	$followingRepo = new Filta\Repository\FollowingRepository;
	$search = $followingRepo->search($userID, $term);

	return Response::json($search);
});

// Route::get('faker', function() {

//     set_time_limit(0);
//     ini_set('max_execution_time', 1000);
//     ini_set('memory_limit','10G');
//     error_reporting(E_ALL);
//     ini_set('display_errors', 1);

//     $faker = Faker\Factory::create();

//     $users = array();
//     $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

//     $password = Hash::make('scott_riley_m8s');

//     $twitterUserID = $faker->randomDigitNotNull;
//     $name = $faker->name;
//     $userName = $faker->userName;
//     $city = $faker->city;
//     $sentence = $faker->sentence(6);
//     $url = $faker->url;
//     $imageUrl = $faker->imageUrl(90, 90);

//     for($i = 0; $i <= 300000; $i++)
//     {
//         $users[] = array(
//             'email' => 'omgwtflolm8'.$i.'@hotmail.com',
//             'password' => $password,
//             'twitter_user_id' => $twitterUserID,
//             'twitter_user_name' => $name,
//             'twitter_user_screen_name' => $userName,
//             'twitter_user_location' => $city,
//             'twitter_user_description' => $sentence,
//             'twitter_user_url' => $url,
//             'twitter_user_profile_image_url' => $imageUrl,
//             'twitter_user_profile_image_url_https' => $imageUrl,
//             'activated' => true,
//             'created_at' => $now,
//             'updated_at' => $now
//         );
//     }

//     foreach(array_chunk($users, 5000) as $userChunk)
//     {
//          User::insert($userChunk);
//     }
    
//     // $user = Sentry::getUser();

//     // $syncFriends = User::where('id', '!=', $user->id)->lists('id');

//     // foreach(array_chunk($syncFriends, 50000) as $syncFriendsChunk)
//     // {
//     //      $user->following()->sync($syncFriendsChunk, false);
//     // }
// });