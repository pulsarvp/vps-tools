{Form assign='f' id="f-apiapp-create" layout="inline" options=[ 'class' => 'pull-right', 'style' => 'margin-bottom: 15px' ]}
{$f->field($appnew, 'name', [ 'enableError' => true ])->textInput(['placeholder'=>{Yii::tr('Name',[],'apiapp')}])}
{if isset($error)}$error{/if}
	<input type="hidden" name="method" value="apiapp-add">
{Html::buttonFa({Yii::tr('Add new application',[],'apiapp')},'plus', [ 'class' => 'btn btn-success apiapp-create'])}
{/Form}
<table class="table table-striped table-bordered" id="apiapp-list">
	<thead>
		<tr>
			<th>{Yii::tr('ID',[],'apiapp')}</th>
			<th>{Yii::tr('Name',[],'apiapp')}</th>
			<th>{Yii::tr('Token',[],'apiapp')}</th>
			<th style="width: 1px"></th>
		</tr>
	</thead>
	<tbody>
		{foreach $apiapps as $apiapp}
			<tr id="{$apiapp->name}" data-id="{$apiapp->id}">
				<td class="id">{$apiapp->id}</td>
				<td class="name">{$apiapp->name}</td>
				<td class="token">{$apiapp->token}</td>
				<td class="control nowrap">
					<div class="edit">
						{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-xs btn-primary apiapp-edit', 'title' => Yii::tr('Edit',[],'apiapp') ])}
						{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger apiapp-delete', 'title' => Yii::tr('Remove',[],'apiapp'), 'data-toggle'=>'confirmation', 'data-title'=>{Yii::tr('Remove?',[],'apiapp')}  ])}
					</div>

					<div class="save" style="display: none">
						{Html::buttonFa('', 'check', [ 'class' => 'btn btn-xs btn-success apiapp-save', 'title' => Yii::tr('Save',[],'apiapp') ])}
						{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger apiapp-close', 'title' => Yii::tr('Close',[],'apiapp') ])}
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<script>
	$(document).ready(function () {
		var oldName  = null;
		var oldToken = null;

		function closeApiapp (tr, name = null, token = null) {
			if (tr.length == 0)
				return;

			if (name == null)
				name = tr.find("td.name").text();
			if (token == null)
				token = tr.find("td.token").text();

			tr.find('td.name').html(name).removeClass('info').attr('contenteditable', false);
			tr.find('td.token').html(token).removeClass('info').attr('contenteditable', false);

			tr.find('.control .save').hide();
			tr.find('.control .edit').show();

			$('.apiapp-edit').attr('disabled', false);
			$('.apiapp-delete').attr('disabled', false);
			$('tr').removeClass('active');
		}

		function saveApiapp () {
			var tr = $('tr.active');
			if (tr.length == 0)
				return;

			var id       = tr.data('id');
			var newName  = tr.find('td.name').text();
			var newToken = tr.find('td.token').text();

			jQuery.ajax({
				url      : '{Url::toRoute('app/edit')}',
				type     : 'POST',
				data     : {
					'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
					id                                : id,
					name                              : newName,
					token                             : newToken
				},
				dataType : "json",
				success  : function (data) {
					if (data != 0) {
						tr.addClass('danger');
						closeApiapp(tr, oldName, oldToken);
						if (data.token)
							tr.find('td.token').append('<p class="error">' + data.token + '</p>');
						if (data.name)
							tr.find('td.name').append('<p class="error">' + data.name + '</p>');
					}
					else {
						tr.addClass('success');
						closeApiapp(tr);
					}
				}
			});
		}

		function deleteApiapp (tr) {

			var id = tr.data('id');

			jQuery.ajax({
				url      : '{Url::toRoute('app/delete')}',
				type     : 'POST',
				data     : {
					'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
					'id'                              : id
				},
				dataType : "json",
				success  : function (data) {
					if (data != 0) {
						tr.addClass('danger');
						closeApiapp(tr);
					}
					else {
						closeApiapp(tr);
						tr.remove();
					}
				}
			});
		}

		$('.apiapp-delete').click(function () {
			var tr = $(this).parents('tr');
			deleteApiapp(tr);
		});
		$('.apiapp-create').click(function () {
			$('#f-apiapp-create').submit();
		});
		$('.apiapp-edit').click(function () {
			$('tr').removeClass('success').removeClass('danger');
			$('tr p.error').remove();

			var tr      = $(this).parents('tr');
			var tdName  = tr.find('td.name');
			var tdToken = tr.find('td.token');
			oldName     = tdName.html();
			oldToken    = tdToken.html();

			tdName.attr('contenteditable', true).addClass('info');
			tdToken.attr('contenteditable', true).addClass('info');

			tr.find('.control .edit').hide();
			tr.find('.control .save').show();

			tr.addClass('active');
			$('.apiapp-edit').attr('disabled', true);
			$('.apiapp-delete').attr('disabled', true);
		});

		$('.apiapp-close').click(function () {
			closeApiapp($(this).parents('tr'), oldName, oldToken);
		});

		$('.apiapp-save').click(function () {
			saveApiapp();
		});

		$(document).keyup(function (e) {
			switch (e.keyCode) {
				case 27:
					closeApiapp();
					break;
				case  13:
					saveApiapp();
					break;
			}
		});
		if (window.location.hash != '') {
			var hash = window.location.hash;
			$(hash).addClass('success');
		}
	});
</script>
