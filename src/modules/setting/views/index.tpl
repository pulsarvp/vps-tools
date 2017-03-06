<h1>{$title}</h1>
<table class="table table-striped table-bordered" id="setting-list">
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
			<tr id="{$setting->name}" data-name="{$setting->name}">
				<td class="name">{$setting->name}</td>
				<td class="value">{$setting->value}</td>
				<td class="description">{$setting->description}</td>
				<td class="control nowrap">
					<div class="edit">
						{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-xs btn-primary setting-edit', 'title' => Yii::tr('Edit') ])}
					</div>
					<div class="save" style="display: none">
						{Html::buttonFa('', 'check', [ 'class' => 'btn btn-xs btn-success setting-save', 'title' => Yii::tr('Save') ])}
						{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger setting-close', 'title' => Yii::tr('Close') ])}
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<script>
	$(document).ready(function () {
		function closeSetting () {
			var tr = $('tr.active');
			if (tr.length == 0)
				return;

			var value       = tr.find('td.value').text();
			var description = tr.find("td.description").html();

			tr.find('td.value').html(value).removeClass('info').attr('contenteditable', false);
			tr.find('td.description').html(description).removeClass('info').attr('contenteditable', false);

			tr.find('.control .save').hide();
			tr.find('.control .edit').show();

			$('.setting-edit').attr('disabled', false);
			$('tr').removeClass('active');
		}

		function saveSetting () {
			var tr = $('tr.active');
			if (tr.length == 0)
				return;

			var name           = tr.data('name');
			var newValue       = tr.find('td.value').text();
			var newDescription = tr.find('td.description').html();

			jQuery.ajax({
				url      : '{Url::toRoute('setting/edit')}',
				type     : 'POST',
				data     : {
					'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
					name                              : name,
					value                             : newValue,
					description                       : newDescription
				},
				dataType : "json",
				success  : function (data) {
					if (data != 0) {
						tr.addClass('danger');
						closeSetting(tr);
						tr.find('td.value').append(data);
					}
					else {
						tr.addClass('success');
						closeSetting(tr);
					}
				}
			});
		}

		$('.setting-edit').click(function () {
			$('tr').removeClass('success');

			var tr            = $(this).parents('tr');
			var tdValue       = tr.find('td.value');
			var tdDescription = tr.find('td.description');
			var value         = tdValue.html();
			var description   = tdDescription.html();

			tdValue.attr('contenteditable', true).addClass('info');
			tdDescription.attr('contenteditable', true).addClass('info');
			tdValue.text(value);

			tr.find('.control .edit').hide();
			tr.find('.control .save').show();

			tr.addClass('active');
			$('.setting-edit').attr('disabled', true);
		});

		$('.setting-close').click(function () {
			closeSetting($(this).parents('tr'));
		});

		$('.setting-save').click(function () {
			saveSetting();
		});

		$(document).keyup(function (e) {
			switch (e.keyCode) {
				case 27:
					closeSetting();
					break;
				case  13:
					saveSetting();
					break;
			}
		});
	});
</script>

