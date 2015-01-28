<?php namespace Filta\Repository;

	use User;
	use Cache;

	class FollowingRepository {

		public function search($userID, $term, $perPage = 50)
		{
			$results = $this->paginate($userID, $term, $perPage);

			$data['records'] = $results->getItems();
			$data['pagination'] = [
				'count'		   => $results->count(),
		        'total'        => $results->getTotal(),
		        'per_page'     => $results->getPerPage(),
		        'current_page' => $results->getCurrentPage(),
		        'last_page'    => $results->getLastPage(),
		        'from'         => $results->getFrom(),
		        'to'           => $results->getTo(),
		        'term'		   => $term
		    ];

		    return $data;
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
					$query->where('twitter_user_location', 'LIKE', "%{$term}%");
		            $query->orWhere('twitter_user_description', 'LIKE', "%{$term}%");
				});
			}
			
			return $following->paginate($perPage);
		}
	}