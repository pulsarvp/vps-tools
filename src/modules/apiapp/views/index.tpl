{if Yii::$app->user->identity->isPermission('admin_apiapp')}
	<table class="table table-striped table-bordered" id="apiapp-list">
		<thead>
			<tr>
				<th>{Yii::tr('ID',[],'apiapp')}</th>
				<th>{Yii::tr('Name',[],'apiapp')}</th>
				<th>{Yii::tr('Token',[],'apiapp')}</th>
				<th style="width: 12%"></th>
			</tr>
		</thead>
		<tbody>
			{foreach $apiapps as $apiapp}
				<tr id="{$apiapp->name}" data-id="{$apiapp->id}">
					<td class="id">{$apiapp->id}</td>
					<td class="name">{$apiapp->name}</td>
					<td class="token">
						{$apiapp->token}
					</td>
					<td class="control">
						<span class="edit">
							{Html::buttonFa('', 'pencil', [ 'class' => 'btn btn-sm btn-info apiapp-edit', 'title' => Yii::tr('Edit',[],'apiapp') ])}
						</span>

						<span class="save" style="display: none">
							{Html::buttonFa('', 'check', [ 'class' => 'btn btn-sm btn-success apiapp-save', 'title' => Yii::tr('Save',[],'apiapp') ])}
						</span>
						{Html::a(
							Html::fa('remove'),
							Url::toRoute(['appapi/index', 'action' => 'delete', 'id' => $apiapp->id]),
							[
								'class' => 'btn btn-sm btn-danger',
								'title' => Yii::tr('Remove application API?',[],'apiapp'),
								'data-toggle'=>'confirmation',
								'data-btn-ok-class'=>'btn-xs btn-danger',
								'data-title'=>Yii::tr('Remove application API?',[],'apiapp'),
								'data-btn-ok-label'=>Yii::tr('Yes',[],'apiapp'),
								'data-btn-cancel-label'=>Yii::tr('No',[],'apiapp')
							]
						)}
					</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
	{Html::buttonFa({Yii::tr('Add new application',[],'apiapp')},'plus', [ 'class' => 'btn btn-success','data-toggle'=>"modal", 'data-target'=>"#form"])}
	<div class="modal fade" id="form" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					{Form assign='f' id="f-apiapp"}
						<div class="form-group field-apiapp-name required">
							<label class="control-label col-md-3" for="apiapp-name">{Yii::tr('Name',[],'apiapp')}</label>
							<input type="text" name="name" class="form-control" placeholder="{Yii::tr('Name',[],'apiapp')}" value="">
							{if count($appnew->errors) > 0}
								<div class="has-error">
									<div class="help-block help-block-error error-block">
										{$appnew->getFirstError('name')}
									</div>
								</div>
							{/if}
						</div>
						<input type="hidden" name="id" value="0">
						<input type="hidden" name="method" value="apiapp">
					{/Form}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary apiapp-submit">{Yii::tr('Save')}</button>
				</div>
			</div>
		</div>
	</div>
	{if count($appnew->errors) > 0}
		<script>
			$('#form').modal('show');
		</script>
	{/if}
	<script>
		$(document).ready(function () {

			$('.apiapp-submit').click(function () {
				$('#f-apiapp').submit();
			});
			$('.apiapp-edit').click(function () {
				var tr = $(this).parents('tr');
				$('input[name="id"]').val(tr.find('td.id').html());
				$('input[name="name"]').val(tr.find('td.name').html());
				$('#form').modal('show');
			});

			$(document).keyup(function (e) {
				switch (e.keyCode) {
					case 27:
						$('#form').modal('hide');
						break;
					case  13:
						$('.apiapp-submit').click();
						break;
				}
			});
			if (window.location.hash != '') {
				var hash = window.location.hash;
				$(hash).addClass('success');
			}
		});
	</script>
{else}
	<div class="text-danger">{Yii::tr('Попытка взлома detected!')}</div>
{/if}