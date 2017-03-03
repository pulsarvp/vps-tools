<h1>{$title}</h1>
{if in_array('create',$module->permission)}
	{Form layout="inline" assign='f' id="f-apiapp-create"}
	{$f->field($appnew, 'name')->textInput(['placeholder'=>{Yii::tr('Name')}])}
	{$f->field($appnew, 'token')->textInput(['placeholder'=>{Yii::tr('Token')}])}
		<input type="hidden" name="method" value="apiapp-add">
	{Html::buttonFa('Create','plus', [ 'class' => 'btn btn-primary apiapp-create'])}
	{/Form}
{/if}
<table class="table table-hover table-striped table-bordered" id="apiapp-list">
	<thead>
		<tr>
			<th>{Yii::tr('ID')}</th>
			<th>{Yii::tr('Name')}</th>
			<th>{Yii::tr('Token')}</th>
			<th></th>
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
						{if in_array('edit',$module->permission)}
							{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-xs btn-primary apiapp-edit', 'title' => Yii::tr('Edit') ])}{/if}
						{if in_array('delete',$module->permission)}
							{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger apiapp-delete', 'title' => Yii::tr('Remove'), 'data-toggle'=>'confirmation', 'data-title'=>{Yii::tr('Remove?')}  ])}
						{/if}
					</div>

					<div class="save" style="display: none">
						{Html::buttonFa('', 'check', [ 'class' => 'btn btn-xs btn-success apiapp-save', 'title' => Yii::tr('Save') ])}
						{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger apiapp-close', 'title' => Yii::tr('Close') ])}
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<script>
	$(document).ready(function () {
		function closeApiapp (tr) {
			if (tr.length == 0)
				return;

			var name  = tr.find("input[name='name']").val();
			var token = tr.find("input[name='token']").val();

			tr.find('td.name').html(name);
			tr.find('td.token').html(token);

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
			var newName  = tr.find('input[name="name"]').val();
			var newToken = tr.find('input[name="token"]').val();

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
						closeApiapp(tr);
						tr.find('td.value').append(data);
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
			$('tr').removeClass('success');

			var tr      = $(this).parents('tr');
			var tdName  = tr.find('td.name');
			var tdToken = tr.find('td.token');
			var name    = tdName.html();
			var token   = tdToken.html();

			tdName.html($("<input>", {
				"name"  : "name",
				"class" : "form-control input-sm"
			}).val(name));

			tdToken.html($("<input>", {
				"name"  : "token",
				"class" : "form-control input-sm"
			}).val(token));

			tr.find('.control .edit').hide();
			tr.find('.control .save').show();

			tr.addClass('active');
			$('.apiapp-edit').attr('disabled', true);
			$('.apiapp-delete').attr('disabled', true);
		});

		$('.apiapp-close').click(function () {
			closeApiapp($(this).parents('tr'));
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
			console.log(hash);
			$(hash).addClass('success');

		}
	});
</script>
