<script>
	$(document).on('click', '#filter', function (e) {
		var path   = [];
		var search = $('#search').val();
		var userID = $('#filter-userID').val();
		var type   = $('#filter-type').val();
		var fromDt = $('#filter-from').val();
		var toDt   = $('#filter-to').val();
		if (search != '')
			path.push('search=' + search);
		if (fromDt != '')
			path.push('from=' + fromDt);
		if (toDt != '')
			path.push('to=' + toDt);
		if (type != '')
			path.push('type=' + type);
		{if isset($users)}
		if (userID != '')
			path.push('userID=' + userID);
		{else}
			path.push('id={$user->id}');
		{/if}
		window.location.href = '{Url::toRoute($url)}?' + path.join("&");
	});
	$(document).on('click', '#reset', function (e) {
		window.location.href = '{Url::toRoute($url)}';
	});
</script>