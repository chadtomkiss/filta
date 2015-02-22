<?php

	class Search extends Eloquent {
		protected $table = 'user_search';

		public function user()
		{
			return $this->belongsTo('User');
		}
	}