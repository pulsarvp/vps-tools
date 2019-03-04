{if Yii::$app->user->identity->hasPermission(['admin_log'])}
	{include file='@logViews/list.tpl'}
	{include file='@logViews/filterjs.tpl' url='/log/index'}
{else}
	<div class="text-danger">{Yii::tr('You have no permission to access this page.')}</div>
{/if}