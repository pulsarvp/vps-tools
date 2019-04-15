<div class="object-filters form-inline">
	<div class="filter">
		<a href="?reset" class="tltp btn btn-default" title="{Yii::tr('Reset filters')}">
			<i class="fa fa-ban"></i></a>
	</div>
	<div class="filter form-group">
		<select name="filter-channel" class="selectpicker" id="filter-channel" title="{Yii::tr('All channels',[],'queue')}" data-live-search="true">
			{foreach $channels as $channel}
				<option value="{$channel}" {if isset($filterChannel) and $filterChannel==$channel}selected{/if}>{$channel}</option>
			{/foreach}
		</select>
	</div>
</div>
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
				<td class="text-center">{$queue->id}
					{if $queue->job}
						<i data-toggle="tooltip" data-title='{$queue->job}' class="fa fa-info-circle"></i>
					{/if}
				</td>
				<td class="text-center">{$queue->pid}</td>
				<td class="text-center">{$queue->channel}</td>
				<td class="text-center">{$queue->ttr}</td>
				<td class="text-center">{$queue->delay}</td>
				<td class="text-center priority">
					<span class="priority-value">{$queue->priority}</span>
					{if empty($queue->pid)}
						<btn class="btn btn-xs btn-success priority-edit"><i class="fa fa-pencil"></i></btn>
						<div class="priority-edit-buttons">
							<btn class="btn btn-xs btn-success priority-edit-save"><i class="fa fa-check"></i></btn>
							<btn class="btn btn-xs btn-danger priority-edit-cancel"><i class="fa fa-ban"></i></btn>
						</div>
						<p class="text-danger error" style="display: none;"></p>
					{/if}
				</td>
				<td class="text-center">{Yii::$app->formatter->asDatetime($queue->pushed_at)}</td>
				<td class="text-center">{Yii::$app->formatter->asDatetime($queue->reserved_at)}</td>
				<td class="text-center">
					{if $queue->done_at!=null}
						{Yii::$app->formatter->asDatetime($queue->done_at)}
						{if $queue->attempt}
							<i data-toggle="tooltip" data-title='{Yii::tr('Количество попыток: {n}',['n'=>$queue->attempt],'queue')}' class="fa fa-info-circle"></i>
						{/if}
					{/if}
				</td>
				<td class="text-center">
					{if $queue->canceled_at!=null}
						{Yii::$app->formatter->asDatetime($queue->canceled_at)}
					{else}
						{Html::a(Html::fa('ban'),{Url::toRoute(['queue/cancel-queue','id'=>$queue->id])},['class'=>'btn btn-xs btn-danger', 'data-id'=>$queue->id, 'data-toggle'=>'confirmation', 'data-title'=>{Yii::tr('Are you sure you want to cancel this job?',[],'queue')}, 'title'=>{Yii::tr('Are you sure you want to cancel this job?',[],'queue')}, 'data-btn-ok-label'=>"{Yii::tr('Yes', [], 'queue')}",'data-btn-ok-class'=>"btn btn-xs btn-danger", 'data-btn-cancel-label'=>"{Yii::tr('No', [], 'queue')}"  ])}
					{/if}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{include file='@queueViews/pagination.tpl'}
<script>
	$().ready(function () {
		$('[data-toggle="tooltip"]').tooltip();
		$('[data-toggle=confirmation]').confirmation({
			rootSelector : '[data-toggle=confirmation]',
			popout       : true
		});
		$(document).keyup(function (e) {
			if (e.keyCode === 27) {
				$('[data-toggle=confirmation]').confirmation('hide');
			}
		});
	});
	$('#filter-channel').on('hide.bs.select', function () {
		window.location.href = '?filterChannel=' + $(this).val();
	});

	function closePriorityEdit (row) {
		var prioritySpan = row.find('.priority-value'),
		    buttons      = row.find('.priority-edit-buttons'),
		    editButton   = row.find('.priority-edit');

		row.removeClass('danger edit');
		row.find('.error').hide();

		buttons.hide();
		editButton.show();
		prioritySpan
			.removeClass('form-control')
			.text(prioritySpan.attr('data-original-value'))
			.attr('contenteditable', false);
	}

	$('.priority-edit').click(function () {
		$(this).hide();

		$('tr.edit').each(function (i, item) {
			closePriorityEdit($(item));
		});

		var row           = $(this).closest('tr'),
		    prioritySpan  = row.find('.priority-value'),
		    priorityValue = prioritySpan.text(),
		    buttons       = row.find('.priority-edit-buttons');

		buttons.show();

		row.addClass('edit').removeClass('danger').find('.error').hide();

		prioritySpan
			.attr('data-original-value', priorityValue)
			.addClass('form-control')
			.attr('contenteditable', true)
			.focus();
	});

	$('.priority-edit-cancel').click(function () {
		closePriorityEdit($(this).closest('tr'))
	});

	$('.priority-edit-save').click(function () {
		var row           = $(this).closest('tr'),
		    prioritySpan  = row.find('.priority-value'),
		    priorityValue = prioritySpan.text();

		jQuery.ajax({
			url      : '{Url::toRoute('queue/change-priority')}',
			type     : 'POST',
			data     : {
				'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
				queueId                           : row.data('id'),
				priority                          : priorityValue,
			},
			dataType : "json",
			success  : function (data) {
				if (data.error) {
					row.addClass('danger').find('.error').html(data.error).show();
					return;
				}

				prioritySpan.attr('data-original-value', data);
				closePriorityEdit(row);
			}
		}).fail(function () {
			closePriorityEdit(row);
		});
	});
</script>
