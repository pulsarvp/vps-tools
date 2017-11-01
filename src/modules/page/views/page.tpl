<div class="page">
	{if $page}
		<h1>{$page->title}</h1>
		<h5>{$page->guid}</h5>
		<h6>{Yii::$app->formatter->asDatetime($page->dt)}</h6>
		<div class="text">{$page->text}</div>
	{/if}
</div>