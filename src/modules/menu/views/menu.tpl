<ul class="{$classUl}">
	{foreach $menus as $menu}
		<li{if $menu->active} class='active'{/if}>{Html::a($menu->name, $menu->url)}</li>
	{/foreach}
</ul>