<ul class="{$classUl}">
	{foreach Yii::$app->menu->forType($menutype)  as $menu}
		<li class="{if $menu->active}active {/if}level-{$menu->depth}">{Html::a($menu->title, $menu->url)}</li>
	{/foreach}
</ul>