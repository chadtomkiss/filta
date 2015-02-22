<div id="following-table-container">
	@if($users->count())
		<table id="myTable" class="ranking-table">
			<thead>
				<tr>
					<th style="width: 30%" colspan="2">Name</th>
					<th style="width: 25%">Location</th>
					<th>Description</th>
				</tr>
			</thead>
		    <tbody>
		    @foreach($users as $user)
		        <tr class="ranking-row">
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

		{{ $users->links() }}
	@else
		Nobody loves {{ Input::get('query') }} :(
	@endif
</div>