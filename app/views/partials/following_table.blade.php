<div id="following-table-container">
	@if($users->count())
		<table class="following-table js-following-table">
			<thead>
				<tr>
					<th class="header-name" colspan="2">Name</th>
					<th class="header-location">Location</th>
					<th>Description</th>
				</tr>
			</thead>
		    <tbody>
		    @foreach($users as $user)
		        <tr>
		        	<td>
		        		<img src="{{ $user->twitter_user_profile_image_url_https }}" class="avatar" />
					</td>
					<td>
						<a href="https://twitter.com/{{ $user->twitter_user_screen_name }}" class="username-link">
							{{ $user->twitter_user_name }}<br />
							{{ "@{$user->twitter_user_screen_name}" }}
						</a>
					</td>
					<td>{{ $user->twitter_user_location }}</td>
					<td>{{ $user->twitter_user_description }}</td>
				</tr>
		    @endforeach
		    </tbody>
		</table>

		@if($users->getLastPage() > 1)
			<div class="pagination_container">
				{{ $users->links() }}
			</div>
		@endif
	@else
		Nobody loves {{ Input::get('query') }} :(
	@endif
</div>