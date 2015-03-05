var FILTA = {
	initilize: function(term) {
		var words = term.split(' ');
		var save_search = $('.js-save-search');

		if(term != '' && $('.js-following-table tr').length)
		{
			save_search.css('display', 'block');
		}
		else
		{
			save_search.css('display', 'none');
		}

		$.each(words, function(i, word) {
			$('.js-following-table tbody').highlight(word);
		});
	},
	searchData : function(term) {
		$.ajax({
			url:"following/search",
			method:'get',
			data: { query: term },
			success: function(resHTML) {
				$('#following-table-container').replaceWith(resHTML);

				var search = $('#js-search-followers').val();
				var save_search = $('.js-save-search');

				var words = search.split(' ');

				if(term != '' && $('.js-following-table tr').length)
				{
					save_search.css('display', 'block');
				}
				else
				{
					save_search.css('display', 'none');
				}

				window.history.pushState({}, '', '/dashboard?query=' + search);

				$.each(words, function(i, word) {
					$('.js-following-table tbody').highlight(word);
				});
			}
		});
	},
	deleteSearch: function(form) {
		var method = form.find("input[name='_method']").val() || 'POST';
		var search_id = form.find("input[name='search_id']").val();

		$.ajax({
			url: "following/search/delete",
			type: method,
			data: form.serialize(),
			success: function(resHTML) {
				form.closest('li').remove();

				if($('.saved_search li').length == 0) {
					$('.saved_search').remove();
				}
			}
		});
	}
};

$(document).ready(function(){
	var search = $('#js-search-followers');
	var save_search = $('.js-save-search');

	FILTA.initilize(search.val());

	search.keyup( $.debounce(400, function(e) {
		var query = $(this).val();

		FILTA.searchData(query);
	}));


	$(".js-saved-search-list li").hover(
		function() {
			$(this).addClass("selected").siblings("li").removeClass("selected");
		},
		function() {
			$(this).removeClass("selected");
		}
	);

	$('.js-delete-search').submit(function(e) {
		e.preventDefault();

		var form = $(this);
		FILTA.deleteSearch(form);
	});

	$(".js-confirm-delete").on("click",function(event){
		return confirm("Are you sure you want to delete?");
	});
});