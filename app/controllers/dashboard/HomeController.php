<?php namespace Dashboard;

	use BaseController;
	use Sentry;
	use View;
	use User;
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

			return View::make('dashboard.index', $data);
		}
	}