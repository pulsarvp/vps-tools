{if $message}
	<div class="noitifcation-container">
		<div class="notification alert alert-{$message['class']}">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			{$message['message']}
		</div>
	</div>
{/if}
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
					<th>{Yii::tr('Rules', [], 'setting')}</th>
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
							<td class="type" data-rule='{$setting->rule}'>{$setting->type}</td>
							<td class="type">{$setting->rule}</td>
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
			{if $canEdit}
			$(document).on('focus', 'tr.active', function () {
				$(this).find('p.error').remove();
			});

			var hidden         = false;
			var valueOld       = '';
			var descriptionOld = '';
			var editor         = '';

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

				var name     = tr.data('name');
				var newValue = tr.find('td.value').text();
				if (tr.find('td.value').find('input:checked').val() !== undefined)
					newValue = tr.find('td.value').find('input:checked').val();
				else if (tr.find('td.value').find('input').val() !== undefined)
					newValue = tr.find('td.value').find('input').val();
				else if (tr.find('td.value').find('select').val() !== undefined)
					newValue = tr.find('td.value').find('select').val();
				else if (tr.find('td.value').find('textarea').html() !== undefined)
					newValue = editor.getValue();
				else
					newValue = tr.find('td.value').text();
				console.log(newValue);
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
				var tdType        = tr.find('td.type');
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
				getForm(valueOld, tdValue, tdType);
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

			function getForm (value, td, type) {
				switch (type.html()) {
					case 'boolean':
						booleanInput(value, td);
						break;
					case 'email':
						emailInput(value, td);
						break;
					case 'ip':
						ipInput(value, td);
						break;
					case 'json':
						jsonInput(value, td);
						break;
					case 'in':
						selectInput(value, td, type.data('rule'));
						break;
					case 'date':
						if (typeof Cleave !== "undefined") {
							dataInput(value, td);
							break;
						}
					case 'integer':
						if (typeof Cleave !== "undefined") {
							integerInput(value, td);
							break;
						}
					case 'datetime':
						if (typeof Cleave !== "undefined") {
							dateTimeInput(value, td);
							break;
						}
					case 'time':
						if (typeof Cleave !== "undefined") {
							timeInput(value, td);
							break;
						}
					default:
						defaultForm(value, td);
						break;
				}
				td.addClass('info');
			}

			function defaultForm (value, td) {
				td.attr('contenteditable', true);
				td.text(value.trim());
			}

			function dataInput (value, td) {
				var input  = $('.input-cleave').clone();
				var cleave = new Cleave(input, {
					date        : true,
					datePattern : [ 'Y', 'm', 'd' ],
					delimiter   : '-',
					uppercase   : true
				});
				input.attr('placeholder', 'YYYY-MM-DD');
				input.val(value.trim());
				td.html(input);
			}

			function timeInput (value, td) {
				var input = $('.input-cleave').clone();

				var cleave = new Cleave(input, {
					delimiter : ':',
					blocks    : [ 2, 2, 2 ]
				});
				input.attr('placeholder', 'HH:mm:ss');
				input.val(value.trim());
				td.html(input);
			}

			function dateTimeInput (value, td) {
				var input = $('.input-cleave').clone();

				var cleave = new Cleave(input, {
					delimiters : [ '-', '-', ' ', ':', ':' ],
					blocks     : [ 4, 2, 2, 2, 2, 2 ]
				});
				input.attr('placeholder', 'YYYY-MM-DD HH:mm:ss');
				input.val(value.trim());
				td.html(input);
			}

			function integerInput (value, td) {
				var input  = $('.input-cleave').clone();
				var cleave = new Cleave(input, {
					numericOnly : true
				});
				input.val(value.trim());
				input.attr('placeholder', '10');
				td.html(input);
			}

			function booleanInput (value, td) {
				var radio = $('.btn-group').clone();
				radio.removeClass('hide');
				if (value.trim() != 0) {
					radio.find('.yes').addClass('active');
					radio.find('.yes').find('input').attr('checked', 'checked');
				}
				else {
					radio.find('.no').addClass('active');
					radio.find('.no').find('input').attr('checked', 'checked');
				}
				td.html(radio);
			}

			function selectInput (value, td, rule) {
				var radio = '<select name="value">';
				$.each(rule.range, function (val) {
					if (value == val)
						radio += '<option value="' + val + '" selected>' + val + '</option>';
					else
						radio += '<option value="' + val + '">' + val + '</option>';
				});
				radio += '</select>';

				td.html(radio);
			}

			function emailInput (value, td) {
				var input = $('.input-cleave').clone();
				input.val(value.trim());
				input.attr('type', 'email');
				input.attr('placeholder', 'example@email.com');
				td.html(input);
			}

			function ipInput (value, td) {
				var input = $('.input-cleave').clone();
				input.val(value.trim());
				input.attr('pattern', "\d{ldelim}1,3{rdelim}\.\d{ldelim}1,3{rdelim}\.\d{ldelim}1,3{rdelim}\.\d{ldelim}1,3{rdelim}");
				input.attr('placeholder', '127.0.0.1');
				td.html(input);
			}

			function jsonInput (value, td) {
				if (typeof CodeMirror != "undefined") {
					var textarea = $('.textarea').clone();
					textarea.attr('id', 'textarea-json');
					textarea.html(value.trim());
					td.html(textarea);
					editor = CodeMirror.fromTextArea(document.getElementById('textarea-json'), {
						lineNumbers : true
					});
				}
				else
					defaultForm(value, td);
			}
			{/if}

		});
	</script>
	<div class="inputs col-md-3 hide">
		<input type="text" name="value" value="" class="input-cleave">
	</div>
	<div class="json col-md-3 hide">
		<textarea name="value" class="textarea"></textarea>
	</div>
	<div class="btn-group col-md-3 hide" data-toggle="buttons">
		<label class="btn btn-primary yes">
			<input type="radio" name="value" value="1" checked>{Yii::$app->formatter->asBoolean(1)}
		</label>
		<label class="btn btn-primary no">
			<input type="radio" name="value" value="0">{Yii::$app->formatter->asBoolean(0)}
		</label>
	</div>
{/if}

