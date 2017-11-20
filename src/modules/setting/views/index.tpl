<table class="table table-striped table-bordered" id="setting-list">
	<thead>
		<tr>
			<th>{Yii::tr('Name', [], 'setting')}</th>
			<th>{Yii::tr('Value', [], 'setting')}</th>
			<th>{Yii::tr('Description', [], 'setting')}</th>
			<th>{Yii::tr('Type', [], 'setting')}</th>
			<th>{Yii::tr('Rule', [], 'setting')}</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		{foreach $settings as $setting}
			<tr id="{$setting->name}" data-name="{$setting->name}">
				<td class="name">{$setting->name}</td>
				<td class="value" data-hidden="{$setting->hidden}">
					{if $setting->hidden}
						***
						<span class="hide">{$setting->value}</span>
					{else}
						{$setting->value}
					{/if}
				</td>
				<td class="description">{$setting->description}</td>
				<td class="type">{$setting->type}</td>
				<td class="rule">{$setting->rule}</td>
				<td class="control nowrap">
					<div class="edit">
						{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-xs btn-primary setting-edit', 'title' => Yii::tr('Edit', [], 'setting') ])}
					</div>
					<div class="save" style="display: none">
						{Html::buttonFa('', 'check', [ 'class' => 'btn btn-xs btn-success setting-save', 'title' => Yii::tr('Save', [], 'setting') ])}
						{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger setting-close', 'title' => Yii::tr('Close', [], 'setting') ])}
					</div>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
<script>
	$(document).ready(function () {
		$(document).on('focus', 'tr.active', function () {
			$(this).find('p.error').remove();
		});

		var hidden         = false;
		var valueOld       = '';
		var descriptionOld = '';

		function closeSetting () {
			var tr = $('tr.active');
			if (tr.length == 0)
				return;
			if (hidden)
				tr.find('td.value').html('***<span class="hide">' + valueOld + '</span>').removeClass('info').attr('contenteditable', false);
			else
				tr.find('td.value').html(valueOld).removeClass('info').attr('contenteditable', false);
			tr.find('td.description').html(descriptionOld).removeClass('info').attr('contenteditable', false);

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
						tr.find('td.value').append('<p class="error">' + data + '</p>');
					}
					else {
						tr.removeClass('danger');
						tr.addClass('success');
						valueOld       = newValue;
						descriptionOld = newDescription;
						closeSetting(tr);
					}
				}
			});
		}

		$('.setting-edit').click(function () {
			$('tr').removeClass('success');
			hidden = false;
			var tr            = $(this).parents('tr');
			var tdValue       = tr.find('td.value');
			var tdDescription = tr.find('td.description');
			if (tdValue.data('hidden')) {
				tdValue.html(tdValue.find('span.hide').html());
				hidden = true;
			}
			valueOld       = tdValue.html();
			descriptionOld = tdDescription.html();

			tdValue.attr('contenteditable', true).addClass('info');
			tdDescription.attr('contenteditable', true).addClass('info');
			tdValue.text(valueOld);
			focusEnd(tdValue.get(0));
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

		$(document).keydown(function (e) {
			switch (e.keyCode) {
				case 27:
					closeSetting();
					return false;
				case  13:
					if (e.shiftKey !== true) {
						saveSetting();
						return false;
					}
			}
		});
		function focusEnd (el) {
			el.focus();
			var range = document.createRange();
			range.selectNodeContents(el);
			range.collapse(false);
			var sel = window.getSelection();
			sel.removeAllRanges();
			sel.addRange(range);
		}
	});
</script>

