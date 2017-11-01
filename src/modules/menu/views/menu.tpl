<ul class="{$classUl}">
	{foreach Yii::$app->menu->forType($menutype)  as $menu}
		<li{if $menu->active} class='active'{/if}>{Html::a($menu->title, $menu->url)}</li>
	{/foreach}
</ul>