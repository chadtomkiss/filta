{{#if records.length}}

<table id="myTable" class="ranking-table">
	<thead>
		<tr>
			<th style="width: 30%" colspan="2">Name</th>
			<th style="width: 25%">Location</th>
			<th>Description</th>
		</tr>
	</thead>
    <tbody>
    {{#each records }}
        <tr class="ranking-row">
        	<td>
        		<img src="{{ this.twitter_user_profile_image_url_https }}" class="avatar" />
			</td>
			<td>
				<a href="https://twitter.com/{{ this.twitter_user_screen_name }}" class="username-link">
					{{ this.twitter_user_name }}<br />
					@{{ this.twitter_user_screen_name }}
				</a>
			</td>
			<td>{{ this.twitter_user_location }}</td>
			<td>{{ this.twitter_user_description }}</td>
		</tr>
    {{/each}}
    </tbody>
</table>

<div class="pagination pagination-centered">
  <ul>
    {{#paginate pagination type="previous"}}
      <li {{#if disabled}}class="disabled"{{/if}}>
      	<a class="page" data-page-number="{{n}}" href="/dashboard?page={{n}}{{#if term}}&query={{term}}{{/if}}" >Prev</a>
      </li>
    {{/paginate}}
    {{#paginate pagination type="middle" limit="7"}}
      <li {{#if active}}class="active"{{/if}}>
      	<a class="page" data-page-number="{{n}}" href="/dashboard?page={{n}}{{#if term}}&query={{term}}{{/if}}">{{n}}</a>
      </li>
    {{/paginate}}
    {{#paginate pagination type="next"}}
      <li {{#if disabled}}class="disabled"{{/if}}>
      	<a class="page" data-page-number="{{n}}" href="/dashboard?page={{n}}{{#if term}}&query={{term}}{{/if}}">Next</a>
      </li>
    {{/paginate}}
  </ul>
</div>
{{else}}
	Nobody loves you :(
{{/if}}