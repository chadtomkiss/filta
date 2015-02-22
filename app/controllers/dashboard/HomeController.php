<?php namespace Dashboard;

	use BaseController;
	use Cache;
	use Sentry;
	use View;
	use User;
	use Search;
	use Input;

	class HomeController extends BaseController {
		
		public function getIndex()
		{
			$user = Sentry::getUser();
			$userID = $user->id;

			$input = Input::all();

			$term = array_get($input, 'query');

			$followingRepo = new \Filta\Repository\FollowingRepository;
			$search = $followingRepo->search($userID, $term);

			$data['users'] = $search;

			$cacheKey = md5('userid.'.$user->id.'.saved_searches');

			if(!Cache::has($cacheKey)) {
				$saved_searches = Search::where('user_id', $user->id)->get();	

				Cache::forever($cacheKey, $saved_searches);
			}
			else {
				$saved_searches = Cache::get($cacheKey);
			}

			$data['saved_searches'] = $saved_searches;

			return View::make('dashboard.index', $data);
		}
	}