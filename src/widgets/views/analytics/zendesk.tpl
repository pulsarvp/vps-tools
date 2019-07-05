<script id="ze-snippet" src="https://static.zdassets.com/ekr/snippet.js?key={$key}"></script>
<script>
	$(document).ready(function () {
		zE('webWidget', 'setLocale', 'ru');
		{if isset($user)}
		zE('webWidget', 'prefill', {
			{if !empty($user.name)}
			name  : {
				value    : '{$user.name}{if !empty($user.untiID)} [U{$user.untiID}]{/if}{if !empty($user.leaderID)} [L{$user.leaderID}]{/if}',
				readOnly : true
			},
			{/if}
			{if !empty($user.email)}
			email : {
				value    : '{$user.email}',
				readOnly : false
			}
			{/if}
		});
		{/if}
	});
</script>