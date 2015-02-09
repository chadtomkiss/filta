<?php namespace Dashboard;

	use BaseController;
	use Sentry;
	use View;
	use User;
	use Input;

	class SearchController extends BaseController {
		
		public function getFollowing()
		{
			$user = Sentry::getUser();
	    	$userID = $user->id;

	    	$input = Input::all();

	    	$term = array_get($input, 'query');

	    	$followingRepo = new \Filta\Repository\FollowingRepository;
	    	$search = $followingRepo->search($userID, $term);

	        $data['users'] = $search;

	        $html = View::make('partials.following_table', $data)->render();

	    	return $html;
		}
	}