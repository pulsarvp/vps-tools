{if $canView}
	{Html::button(Yii::tr('Expand all', [], 'setting'),['class'=>'btn btn-primary collapse-all-show'])}
	{Html::button(Yii::tr('Collapse all', [], 'setting'),['class'=>'btn btn-primary collapse-all-hide'])}
	<table class="table table-striped table-bordered" id="setting-list">
		<thead>
			<tr>
				<th>{Yii::tr('Name', [], 'setting')}</th>
				<th>{Yii::tr('Value', [], 'setting')}</th>
				<th>{Yii::tr('Description', [], 'setting')}</th>
				{if $canEdit}
					<th>{Yii::tr('Type', [], 'setting')}</th>
					<th></th>
				{/if}
			</tr>
		</thead>
		{assign var="in" value=true}
		{foreach $settings as $key=>$groups}
			<thead>
				<tr data-toggle="collapse" data-target="#{$key}" aria-controls="{$key}">
					<th colspan="6" class="info"><span class="setting-group">{$key}</span></th>
				</tr>
			</thead>
			<tbody id="{$key}" class="collapse {if $in}in{/if}">
				{foreach $groups as $setting}
					<tr id="{$setting->name}" data-name="{$setting->name}">
						<td class="name">{$setting->name}</td>
						<td class="value" data-hidden="{$setting->hidden}">
							{if $setting->hidden}
								***
							{else}
								{$setting->value}
							{/if}
						</td>
						<td class="description">{$setting->description}</td>
						{if $canEdit}
							<td class="type">{$setting->type}</td>
							<td class="control nowrap">
								<div class="edit">
									{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-xs btn-primary setting-edit', 'title' => Yii::tr('Edit', [], 'setting') ])}
								</div>
								<div class="save" style="display: none">
									{Html::buttonFa('', 'check', [ 'class' => 'btn btn-xs btn-success setting-save', 'title' => Yii::tr('Save', [], 'setting') ])}
									{Html::buttonFa('', 'remove', [ 'class' => 'btn btn-xs btn-danger setting-close', 'title' => Yii::tr('Close', [], 'setting') ])}
								</div>
							</td>
						{/if}
					</tr>
				{/foreach}
			</tbody>
			{assign var="in" value=false}
		{/foreach}
	</table>
	<script>
		$(document).ready(function () {
			$('.collapse-all-show').on('click', function () {
				$('.collapse').collapse('show');
			});
			$('.collapse-all-hide').on('click', function () {
				$('.collapse').collapse('hide');
			});

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
					tr.find('td.value').html('***').removeClass('info').attr('contenteditable', false);
				else
					tr.find('td.value').html(valueOld).removeClass('info').attr('contenteditable', false);
				tr.find('td.description').html(descriptionOld).removeClass('info').attr('contenteditable', false);

				tr.find('.control .save').hide();
				tr.find('.control .edit').show();

				$('.setting-edit').attr('disabled', false);
				$('tr').removeClass('active');
				tr.removeClass('danger');
			}

			function saveSetting () {
				var tr = $('tr.active');
				if (tr.length == 0)
					return;

				var name           = tr.data('name');
				var newValue       = tr.find('td.value').text();
				var newDescription = tr.find('td.description').html();
				tr.find('p.error').remove();
				jQuery.ajax({
					url      : '{Url::toRoute('setting/edit')}',
					type     : 'POST',
					data     : {
						'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
						name                              : name,
						value                             : newValue.trim(),
						description                       : newDescription.trim()
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
					},
					error    : function (data) {
						if (data != 0) {
							tr.addClass('danger');
							tr.find('td.value').append('<p class="error">' + data.statusText + '</p>');
						}
					}
				});
			}

			function getSetting () {
				var tr = $('tr.active');
				if (tr.length == 0)
					return;

				var name = tr.data('name');

				jQuery.ajax({
					url      : '{Url::toRoute('setting/value')}',
					type     : 'POST',
					data     : {
						'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
						name                              : name
					},
					dataType : "json",
					success  : function (data) {
						tr.find('td.value').text(data)
					}
				});
			}

			$('.setting-edit').click(function () {
				$('tr').removeClass('success');
				hidden = false;
				var tr = $(this).parents('tr');
				tr.addClass('active');

				var tdValue       = tr.find('td.value');
				var tdDescription = tr.find('td.description');
				if (tdValue.data('hidden')) {
					getSetting();
					hidden = true;
				}
				valueOld       = tdValue.html();
				descriptionOld = tdDescription.html();

				tdValue.attr('contenteditable', true).addClass('info');
				tdDescription.attr('contenteditable', true).addClass('info');
				tdValue.text(valueOld.trim());
				focusEnd(tdValue.get(0));
				tr.find('.control .edit').hide();
				tr.find('.control .save').show();

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
{/if}
