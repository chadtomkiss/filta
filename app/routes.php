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

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@getIndex', 'https'));

Route::get('twitter/connect', array('as' => 'twitter.connect', 'uses' => function(){
    // Reqest tokens
    $tokens = Twitter::oAuthRequestToken();

    // Redirect to twitter
    Twitter::oAuthAuthenticate(array_get($tokens, 'oauth_token'));
    exit;
}, 'https'));

Route::get('twitter/auth', array('uses' => function(){
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
}, 'https'));

Route::group(array('before' => 'auth.sentry'), function() {
    Route::get('dashboard', array('as' => 'dashboard', 'uses' => 'Dashboard\HomeController@getIndex', 'https'));

    Route::get('twitter/import', array('as' => 'twitter.import', 'uses' => 'Dashboard\ImportController@getImport', 'https'));
    Route::post('twitter/import', array('as' => 'twitter.import.post', 'uses' => 'Dashboard\ImportController@postImport', 'https'));

    Route::get('following/search', array('as' => 'twitter.search', 'uses' => 'Dashboard\SearchController@getFollowing', 'https'));

    Route::post('following/search/save', array('as' => 'twitter.search.saved.post', 'uses' => 'Dashboard\SearchController@postSave', 'https'));
    Route::put('following/search/delete', array('as' => 'twitter.search.saved.delete', 'uses' => 'Dashboard\SearchController@putDelete', 'https'));

    Route::get('logout', array('as' => 'logout', 'uses' => function() {
        Sentry::logout();

        return Redirect::route('home');
    }, 'https'));
});

// Setup laravel logs
$monolog = Log::getMonolog();
$syslog = new \Monolog\Handler\SyslogHandler('papertrail');
$formatter = new \Monolog\Formatter\LineFormatter('%channel%.%level_name%: %message% %extra%');
$syslog->setFormatter($formatter);

$monolog->pushHandler($syslog);