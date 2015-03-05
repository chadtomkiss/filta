<?php namespace Dashboard;

	use BaseController;
	use Cache;
	use Sentry;
	use View;
	use User;
	use Search;
	use Redirect;
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

		public function postSave()
		{
			$user = Sentry::getUser();

			$query = Input::get('search_followers');

			if($query && !Search::where('query', $query)->where('user_id', $user->id)->count())
			{
				$search = new Search;

				$search->title = $query;
				$search->query = $query;
				$search->user()->associate($user);

				$search->save();

				$cacheKey = md5('userid.'.$user->id.'.saved_searches');
				Cache::forget($cacheKey);
			}

			return Redirect::route('dashboard', array('query' => $query));
		}

		public function putDelete()
		{
			$user = Sentry::getUser();

			$searchID = Input::get('search_id');

			$search = Search::where('user_id', $user->id)->find($searchID);

			if($search)
			{
				$search->delete();

				$cacheKey = md5('userid.'.$user->id.'.saved_searches');
				Cache::forget($cacheKey);
			}

			return;
		}
	}