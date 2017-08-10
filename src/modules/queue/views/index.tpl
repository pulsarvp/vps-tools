<h1>{Yii::tr("Queue management", [], 'queue')}</h1>
<table class="table table-striped table-bordered" id="queue-list">
	<thead>
		<tr>
			{foreach $fields as $key}
				<th>
					{Yii::tr($key|ucfirst,[],'queue')}
					{if isset($sort)}
						{if array_key_exists($key, $sort->attributeOrders)}
							{if $sort->attributeOrders[$key] == SORT_ASC}
								<a class="table-sort-link" href="{Url::current([ 'sort' => "-`$key`",'page' => "" ])}">
									<i class="fa fa-sort-alpha-asc" title="{Yii::tr('Sort descending',[],'queue')}"></i>
								</a>
							{else}
								<a class="table-sort-link" href="{Url::current([ 'sort' => $key,'page' => "" ])}">
									<i class="fa fa-sort-alpha-desc" title="{Yii::tr('Sort ascending',[],'queue')}"></i>
								</a>
							{/if}
						{elseif array_key_exists($key, $sort->attributes)}
							<a class="table-sort-link" href="{Url::current([ 'sort' => $key,'page' => "" ])}">
								<i class="fa fa-sort" title="{Yii::tr('Sort ascending',[],'queue')}"></i>
							</a>
						{/if}
					{/if}
				</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		{foreach $queues as $queue}
			<tr id="{$queue->id}" data-id="{$queue->id}">
				<td class="job">
					<pre>{$queue->job}</pre>
				</td>
				<td class="text-center">{$queue->ttr}</td>
				<td class="text-center">{$queue->delay}</td>
				<td class="text-center">{$queue->priority}</td>
				<td>{Yii::$app->formatter->asDatetime($queue->pushed_at)}</td>
				<td>{Yii::$app->formatter->asDatetime($queue->reserved_at)}</td>
				<td>{Yii::$app->formatter->asDatetime($queue->done_at)}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{include file='@queueViews/pagination.tpl'}
