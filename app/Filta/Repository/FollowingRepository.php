<?php namespace Filta\Repository;

	use User;
	use Cache;

	class FollowingRepository {

		public function search($userID, $term, $perPage = 50)
		{
			$result = $this->paginate($userID, $term, $perPage);

			$result->setBaseUrl('dashboard');
			$result->appends(array('query' => $term));

			return $result;
		}

		public function paginate($userID, $term, $perPage)
		{
			$following = User::select(array(
					'twitter_user_name',
					'twitter_user_screen_name',
					'twitter_user_description',
					'twitter_user_location',
					'twitter_user_profile_image_url',
					'twitter_user_profile_image_url_https',
			))
			->join('twitter_user', 'twitter_user.following_id', '=', 'users.id')
			->where('twitter_user.user_id', $userID);

			if($term)
			{
				$following->where(function($query) use ($term) 
				{
					$parts = explode(' ', $term);

					$safeTerm = array();
					foreach($parts as $part)
					{
						if($part)
						{
							$safeTerm[] = preg_replace('/[^a-zA-Z0-9\s]/', '', trim($part)) . ':*';
						}
					}

					if($safeTerm)
					{
						$search = join('&', $safeTerm);
						$query->whereRaw("searchtext @@ to_tsquery(?)", array($search));
					}
				});
			}
			
			return $following->remember(60)->paginate($perPage);
		}
	}