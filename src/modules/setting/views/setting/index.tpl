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
					{Html::fa('pencil',['class' => 'btn btn-xs btn-success setting-edit', 'title' => Yii::tr('Edit') ])}
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
		tr.find('td.nowrap').html('{Html::fa('pencil',['class' => 'btn btn-xs btn-success setting-edit', 'title' => Yii::tr('Edit') ])}');
	}
	$(document).on('click', '.setting-edit', function () {
		value = $(this).parent().parent().find('td.value').html();
		$(this).parent().parent().find('td.value').html('<textarea name="value">' + value + '</textarea>');
		description = $(this).parent().parent().find('td.description').html();
		$(this).parent().parent().find('td.description').html('<textarea name="description">' + description + '</textarea>');
		$(this).parent().parent().find('td.nowrap').html('{Html::fa('check', [ 'class' => 'btn btn-xs btn-success setting-save', 'title' => Yii::tr('Save') ])}' + '{Html::fa('remove', [ 'class' => 'btn btn-xs btn-danger setting-close', 'title' => Yii::tr('Close') ])}');
	});
	$(document).on('click', '.setting-close', function () {
		closeEdit($(this).parent().parent(), value, description);
	});
	$(document).on('click', '.setting-save', function () {
		var tr             = $(this).parent().parent();
		var name           = tr.find('td.name').html();
		var newValue       = tr.find('textarea[name="value"]').val();
		var newDescription = tr.find('textarea[name="description"]').val();
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
				}
			}
		});
	});

</script>
