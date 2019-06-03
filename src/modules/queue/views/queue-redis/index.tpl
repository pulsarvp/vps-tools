{if Yii::$app->user->identity->hasPermission()}
	<div class="queue-information-index">
		<h2>{Yii::tr('Common information', [], 'queue')}</h2>
		{$detailView}
		<p>
			<i>*TTR</i> - {Yii::tr('Maximum time to complete a task', [], 'queue')}
		</p>
	</div>
	<div class="queue-information-index">
		<h2>{Yii::tr('Jobs', [], 'queue')}</h2>
		<div class="object-filters form-inline">
			<div class="filter">
				<a href="{Url::toRoute(['queue-redis/index'])}" class="btn btn-link" title="{Yii::tr('Reset filters')}">
					{Yii::tr('Reset filters')}
				</a>
			</div>
		</div>
		{$gridView}
	</div>
{else}
	<div class="text-danger">{Yii::tr('You have no permission to access this page.')}</div>
{/if}