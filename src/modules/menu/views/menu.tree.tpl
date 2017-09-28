{assign depth 0}
<ul class="menu-items">
	{foreach Yii::$app->menu->forType( Yii::$app->settings->get( 'backendmain' ) ) as $object}
	{if $depth == $object->depth}
	{if !$object@first}</li>{/if}
	{elseif $object->depth > $depth}
	{if !$object@first}
	<ul class="sub-menu level-{$depth}">{/if}
		{assign depth $object->depth}
		{else}
		{str_repeat('</li></ul>', $depth - $object->depth)}
		</li>
		{assign depth $object->depth}
		{/if}
		<li{if $object->active} class="active"{/if}>{Html::a($object->name, $object->url)}
			{/foreach}
			{if $depth > 1}
			{str_repeat('</li></ul>', $depth-1)}</li>
		{/if}
	</ul>
