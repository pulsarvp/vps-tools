<div class="row">
	<div class="form-group">
		<label for="search">{Yii::tr('Date end',[],'log')}</label>
		<input id="filter-to" value="{if isset($to)}{$to}{/if}" type="text">
	</div>
	<div class="form-group">
		<label for="search">{Yii::tr('Date start',[],'log')}</label>
		<input id="filter-from" value="{if isset($from)}{$from}{/if}" type="text">
	</div>
	<div class="form-group">
		<label for="search">{Yii::tr('Search',[],'log')}</label>
		<input id="search" value="{if isset($search)}{$search}{/if}" type="text">
	</div>
	<div class="form-group">
		<label for="filter-type">{Yii::tr('Type',[],'log')}</label>
		<select id="filter-type" class="selectpicker">
			<option value=""></option>
			{foreach $types as $item}
				<option value="{$item}" {if $item == $type}selected{/if}>{$item}</option>
			{/foreach}
		</select>
	</div>
	<div class="form-group">
		<button type="button" class="btn btn-sm btn-default" id="reset">{Html::fa('ban')}</button>
		<button type="button" class="btn btn-sm btn-primary" id="filter">{Html::fa('check')}</button>
	</div>
</div>
<table class="table table-bordered table-hover" id="log-list">
	<thead>
		<tr>
			{foreach [ 'userID', 'email', 'type', 'action', 'url', 'dt']  as $key}
				<th>
					{Yii::tr(ucfirst($key),[],'log')}
					{if isset($sort)}
						{if array_key_exists($key, $sort->attributeOrders)}
							{if $sort->attributeOrders[$key] == SORT_ASC}
								<a href="{Url::current([ 'sort' => "-`$key`",'page' => "" ])}">
									<i class="fa fa-sort-numeric-asc"></i>
								</a>
							{else}
								<a href="{Url::current([ 'sort' => $key,'page' => "" ])}">
									<i class="fa fa-sort-numeric-desc"></i></a>
							{/if}
						{elseif array_key_exists($key, $sort->attributes)}
							<a href="{Url::current([ 'sort' => $key,'page' => "" ])}"><i class="fa fa-sort"></i>
							</a>
						{/if}
					{/if}
				</th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		{foreach $models as $k=>$model}
			<tr>
				<td class="userID">{$model->userID}</td>
				<td>{$model->email}</td>
				<td class="type">{$model->type}</td>
				<td class="action">{$model->action}</td>
				<td><a href="{$model->url}" target="_blank">{$model->url}</a></td>
				<td class="dt" data-order="{strtotime($model->dt)}">
					<small>{Yii::$app->formatter->asDatetime($model->dt)}</small>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{include file='@vpsViews/pagination.tpl'}

<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title modal-text">{Yii::tr('Loading...',[],'log')}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div><b>{Yii::tr('UserID',[],'log')}:</b> <span class="modal-text" id="modal-userID"></span></div>
				<div><b>{Yii::tr('Email',[],'log')}:</b> <span class="modal-text" id="modal-email"></span></div>
				<div><b>{Yii::tr('Url',[],'log')}:</b> <span class="modal-text" id="modal-url"></span></div>
				<div><b>{Yii::tr('Dt',[],'log')}:</b> <span class="modal-text" id="modal-dt"></span></div>
				<ul class="nav nav-tabs" role="tablist">
					<li class="nav-item">
						<a class="nav-link active" href="#server" role="tab" data-toggle="tab">{Yii::tr('Server', [], 'log')}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#session" role="tab" data-toggle="tab">{Yii::tr('Session', [], 'log')}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#cookie" role="tab" data-toggle="tab">{Yii::tr('Cookie', [], 'log')}</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#post" role="tab" data-toggle="tab">{Yii::tr('Post', [], 'log')}</a>
					</li>
				</ul>
				<div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="server">
					</div>
					<div role="tabpanel" class="tab-pane" id="session">
					</div>
					<div role="tabpanel" class="tab-pane" id="cookie">
					</div>
					<div role="tabpanel" class="tab-pane" id="post">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary apiapp-submit">{Yii::tr('Save')}</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).on('click', 'tr', function () {
		var type   = $(this).find('.type').text();
		var userID = $(this).find('.userID').text();
		var dt     = $(this).find('.dt').data('order');
		$('#viewModal').modal('show');
		jQuery.ajax({
			url      : '{Url::toRoute(['log/json'])}',
			type     : 'POST',
			data     : { _csrf : '{Yii::$app->request->getCsrfToken()}', type : type, userID : userID, dt : dt },
			dataType : "json",
			success  : function (data) {
				$('.modal-title').html(data.action);
				$('#modal-userID').html(data.userID);
				$('#modal-url').html(data.url);
				$('#modal-email').html(data.email);
				$('#modal-dt').html(data.dt);
				$('#server').html('<pre>' + JSON.stringify(data.server, null, ' ') + '</pre>');
				$('#session').html('<pre>' + JSON.stringify(data.session, null, ' ') + '</pre>');
				$('#cookie').html('<pre>' + JSON.stringify(data.cookie, null, ' ') + '</pre>');
				$('#post').html('<pre>' + JSON.stringify(data.post, null, ' ') + '</pre>');

			}
		});
	});
	$('#viewModal').on('hidden.bs.modal', function () {
		$('.modal-text').html('{Yii::tr('Loading...',[],'log')}');
		$('#server').html('');
		$('#session').html('');
		$('#cookie').html('');
		$('#post').html('');
	});
	$(document).on('click', '#filter', function (e) {
		var path   = [];
		var search = $('#search').val();
		var type   = $('#filter-type').val();
		var fromDt = $('#filter-from').val();
		var toDt   = $('#filter-to').val();
		if (search != '')
			path.push('search=' + search);
		if (fromDt != '')
			path.push('from=' + fromDt);
		if (toDt != '')
			path.push('to=' + toDt);
		if (type != '')
			path.push('type=' + type);
		window.location.href = '{Url::toRoute('log/index')}?' + path.join("&");
	});
	$(document).on('click', '#reset', function (e) {
		window.location.href = '{Url::toRoute('log/index')}';
	});
</script>