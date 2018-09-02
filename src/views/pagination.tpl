{if isset($pagination)}
	{use class='vps\tools\widgets\LinkPager' type='block'}
	<div class="pagination-container">
		{LinkPager pagination=$pagination}{/LinkPager}
		<ul class="pagination d-inline-flex">
			<li class="page-item">
				<span class="page-link">{Yii::tr('Total', [], 'widgets/link-pager')}: {$pagination->totalCount}</span>
			</li>
		</ul>
	</div>
{/if}
