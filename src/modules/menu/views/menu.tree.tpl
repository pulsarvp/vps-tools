{assign depth 0}
<ul class="menu-items">
	{foreach Yii::$app->menu->forType($menutype) as $object}
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
		<li class="{if $object->active}active {/if}level-{$object->depth}">{Html::a($object->title, $object->url)}
			{/foreach}
			{if $depth > 1}
			{str_repeat('</li></ul>', $depth-1)}</li>
		{/if}
	</ul>
