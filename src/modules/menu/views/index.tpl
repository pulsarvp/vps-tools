<div class="form-group">
	<div class="col-md-3">
		<select id="menu-type" name="type" class="selectpicker show-tick" title="{Yii::tr('Select', [], 'menu') }" data-width="100%">
			{foreach $menuTypes as $menuType}
				{if isset($type->id) and $menuType->id==$type->id}
					<option value="{$menuType->id}" selected data-content="{$menuType->title}">{$menuType->guid}</option>
				{else}
					<option value="{$menuType->id}" data-content="{$menuType->title}">{$menuType->guid}</option>
				{/if}
			{/foreach}
		</select>
	</div>
	<div class="col-md-3">
		{if isset($type->id)}
			{Html::a(Yii::tr('Add item menu', [], 'menu'),Url::toRoute(['menu/add','type'=>$type->id]),['class'=>'btn btn-primary'])}
		{/if}
	</div>
</div>
{if $menus}
	<table class="table table-hover table-striped" id="rule-list">
		<thead>
			<tr>
				<th>{Yii::tr('Name', [], 'menu')}</th>
				<th>{Yii::tr('Url', [], 'menu')}</th>
				<th>{Yii::tr('Path', [], 'menu')}</th>
				<th>{Yii::tr('Visible', [], 'menu')}</th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</thead>
		<tbody>
			{foreach $menus as $menu}
				<tr data-id="{$menu->id}">
					<td class="depth-{$menu->depth}">{$menu->title}</td>
					<td>{$menu->url}</td>
					<td>{$menu->path}</td>
					<td>
						{if $menu->visible}
							{Html::fa('eye',['class'=>'menu-visible'])}
						{else}
							{Html::fa('eye-slash',['class'=>'menu-visible'])}
						{/if}
					</td>
					<td>{Html::a(Html::fa('pencil'),Url::toRoute(['menu/edit','id'=>$menu->id]),['class'=>'btn btn-xs btn-success menu-edit'])}</td>
					<td>{Html::a(Html::fa('plus'),Url::toRoute(['menu/add','parentID'=>$menu->id,'type'=>$type->id]),['class'=>'btn btn-xs btn-primary menu-add'])}</td>
					<td>{Html::fa('remove',['class'=>'btn btn-xs btn-danger menu-delete','data-toggle'=>'confirmation', 'data-title'=>{Yii::tr('Delete selected menu and all its children?',[],'menu')}, 'title'=>{Yii::tr('Delete selected menu and all its children?',[],'menu')}, 'data-btn-ok-label'=>"{Yii::tr('Yes', [], 'menu')}",'data-btn-ok-class'=>"btn btn-xs btn-danger", 'data-btn-cancel-label'=>"{Yii::tr('No', [], 'menu')}"  ])}</td>
				</tr>
			{/foreach}
		</tbody>
	</table>
{/if}
<script>
	$('#menu-type').on('change', function () {
		window.location.href = '?type=' + this.value;
	});
	$(document).on('click', '.menu-visible', function () {
		var tr = $(this).parent().parent();
		var td = $(this).parent();
		var id = tr.data('id');
		jQuery.ajax({
			url      : '{Url::toRoute('menu/visible')}',
			type     : 'POST',
			data     : {
				'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
				id                                : id
			},
			dataType : "json",
			success  : function (data) {
				if (!jQuery.isNumeric(data)) {
					tr.addClass('danger');
				}
				else {
					if (data)
						td.html('{Html::fa('eye',['class'=>'menu-visible'])}');
					else
						td.html('{Html::fa('eye-slash',['class'=>'menu-visible'])}');
				}
			}
		});
	});
	$(document).on('click', '.menu-delete', function () {
		var tr = $(this).parent().parent();
		var td = $(this).parent();
		var id = tr.data('id');
		jQuery.ajax({
			url      : '{Url::toRoute('menu/delete')}',
			type     : 'POST',
			data     : {
				'{Yii::$app->request->csrfParam}' : '{Yii::$app->request->getCsrfToken()}',
				id                                : id
			},
			dataType : "json",
			success  : function (data) {
				if (!jQuery.isNumeric(data)) {
					tr.addClass('danger');
				}
				else
					tr.remove();
			}
		});
	});
</script>