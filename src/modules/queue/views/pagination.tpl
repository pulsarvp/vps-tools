{if isset($pagination)}
{use class='yii\widgets\LinkPager' type='block'}
<div class="pagination-container">
	{LinkPager pagination=$pagination}{/LinkPager}
	<ul class="pagination">
		<li><span>{Yii::tr('Total',[],'queue')}: {$pagination->totalCount}</span></li>
	</ul>
</div>
{/if}