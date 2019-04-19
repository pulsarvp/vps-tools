<div class="queue-information-index">
	<h2>{Yii::tr('Common information', [], 'queue')}</h2>
	{$detailView}
	<p>
		<i>*TTR</i> - {Yii::tr('Maximum time to complete a task', [], 'queue')}
	</p>
</div>
<div class="queue-information-index">
	<h2>{Yii::tr('Jobs', [], 'queue')}</h2>
	{$gridView}
</div>
