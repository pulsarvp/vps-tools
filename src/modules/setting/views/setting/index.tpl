<div class="title"><h1>{$title}</h1></div>
<table class="table table-hover table-striped" id="setting-list">
	<thead>
		<tr>
			<th>{Yii::tr('Name')}</th>
			<th>{Yii::tr('Value')}</th>
			<th>{Yii::tr('Description')}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach $settings as $setting}
			<tr id="{$setting->name}">
				<td class="name">{$setting->name}</td>
				<td class="value">{$setting->value}</td>
				<td class="description">{$setting->description}</td>
				<td class="nowrap">
					{Html::buttonFa('','pencil',['class' => 'btn btn-xs btn-primary setting-edit', 'title' => Yii::tr('Edit') ])}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<script>
	var value, description;
	function closeEdit (tr, value, description) {
		tr.find('td.description').html(description);
		tr.find('td.value').html(value);
		tr.find('td.nowrap').html('{Html::buttonFa('','pencil',['class' => 'btn btn-xs btn-primary setting-edit', 'title' => Yii::tr('Edit') ])}');
	}
	$(document).on('click', '.setting-edit', function () {
		$('tr').removeClass('success');
		value = $(this).parent().parent().find('td.value').html();
		$(this).parent().parent().find('td.value').html('<input name="value" value="' + value + '" class="form-control"/>');
		description = $(this).parent().parent().find('td.description').html();
		$(this).parent().parent().find('td.description').html('<input name="description" value="' + description + '" class="form-control"/>');
		$(this).parent().parent().find('td.nowrap').html('{Html::buttonFa('','check', [ 'class' => 'btn btn-xs btn-success setting-save', 'title' => Yii::tr('Save') ])}' + '{Html::buttonFa('','remove', [ 'class' => 'btn btn-xs btn-danger setting-close', 'title' => Yii::tr('Close') ])}');
		$('.setting-edit').attr('disabled', true);
	});
	$(document).on('click', '.setting-close', function () {
		closeEdit($(this).parent().parent(), value, description);
		$('.setting-edit').attr('disabled', false);
	});
	$(document).keyup(function (e) {
		switch (e.keyCode) {
			case 27:
				$('.setting-close').click();
				break;
			case  13:
				$('.setting-save').click();
				break;
		}

	});
	$(document).on('click', '.setting-save', function () {
		var tr             = $(this).parent().parent();
		var name           = tr.find('td.name').html();
		var newValue       = tr.find('input[name="value"]').val();
		var newDescription = tr.find('input[name="description"]').val();
		jQuery.ajax({
			url      : '{Url::toRoute('setting/edit')}',
			type     : 'POST',
			data     : { '{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}', name : name, value : newValue, description : newDescription },
			dataType : "json",
			success  : function (data) {

				if (data != 0) {
					tr.addClass('danger');
					tr.find('td.value').append(data);
				}
				else {
					tr.addClass('success');
					closeEdit(tr, newValue, newDescription);
					$('.setting-edit').attr('disabled', false);
				}
			}
		});
	});

</script>
