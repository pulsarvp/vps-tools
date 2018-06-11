<ul class="nav nav-tabs" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" href="#users" role="tab" data-toggle="tab">{Yii::tr('Users', [], 'user')}</a></li>
	<li class="nav-item">
		<a class="nav-link" href="#roles" role="tab" data-toggle="tab">{Yii::tr('Roles', [], 'user')}</a></li>
	<li class="nav-item">
		<a class="nav-link" href="#permission" role="tab" data-toggle="tab">{Yii::tr('Permissions', [], 'user')}</a>
	</li>
</ul>
<div class="tab-content">
	<div role="tabpanel" class="tab-pane container active" id="users">
		<div class="object-filters form-inline">
			<div class="filter form-group">
				<label for="search-user">{Yii::tr('Search:', [], 'user')}</label>
				<input class="form-control" name="search" id="search-user" type="text" value="{if isset($search)}{$search}{/if}">
			</div>
			<div class="filter form-group">
				<label for="select-filter-role">{Yii::tr('Role', [], 'user')}:</label>
				<select class="selectpicker select-filter-role" id="select-filter-role" title="{Yii::tr('Select role', [], 'user')}...">
					<option value="">----</option>
					{foreach $roles as $role}
						<option value="{$role.name}"{if $role.name==$filterRole} selected="selected"{/if}>{$role.name}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<table class="table table-hover table-striped" id="user-list">
			<thead>
				<tr>
					{foreach [ 'id','', 'name', 'email', 'roles', 'active', 'loginDT', 'activeDT']  as $key}
						<th>
							{Yii::tr(ucfirst($key), [], 'user')}
							{if isset($sort)}
								{if array_key_exists($key, $sort->attributeOrders)}
									{if $sort->attributeOrders[$key] == SORT_ASC}
										<a class="sort" href="{Url::current([ 'sort' => "-`$key`",'page' => "" ])}">
											<i class="fa fa-sort-numeric-asc"></i>
										</a>
									{else}
										<a class="sort" href="{Url::current([ 'sort' => $key,'page' => "" ])}">
											<i class="fa fa-sort-numeric-desc"></i>
										</a>
									{/if}
								{elseif array_key_exists($key, $sort->attributes)}
									<a class="sort" href="{Url::current([ 'sort' => $key,'page' => "" ])}">
										<i class="fa fa-sort"></i>
									</a>
								{/if}
							{/if}
						</th>
					{/foreach}
					{if Yii::$app->user->can('admin')}
						<th class="no-sort"></th>
					{/if}
				</tr>
			</thead>
			<tbody>
				{foreach $users as $user}
					<tr>
						<td>{$user['id']}</td>
						<td>{Html::img($user['image'],['class'=>'img-thumbnail','width'=>'50'])}</td>
						<td>{Html::a($user['name'],Url::toRoute(['user/view','id'=>$user['id']]))}</td>
						<td>{$user['email']}</td>
						<td>
							<select class="selectpicker select-user-role" title="{Yii::tr('Select role', [], 'user')}..." data-id="{$user['id']}" multiple {if $user['id'] == Yii::$app->user->id}disabled="1"{/if}>
								{foreach $roles as $role}
									<option value="{$role.name}"{if in_array($role.name,explode(',',$user['rolesNames']))} selected="selected"{/if}>{$role.name}</option>
								{/foreach}
							</select>
						</td>
						<td class="state">
							<button id="btn{$user['id']}" class="btn btn-sm user-state btn-{if !$user['active']}danger{else}success{/if}" {if $user['id'] == Yii::$app->user->id}disabled="1"{/if} data-id="{$user['id']}" data-state="{1 - $user['active']}" title="{if !$user['active']}{Yii::tr('Enable', [], 'user')}{else}{Yii::tr('Disable', [], 'user')}{/if}">
								{if $user['active']}
									{Html::fa('check',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Disable', [], 'user')])}
								{else}
									{Html::fa('ban',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Enable', [], 'user')])}
								{/if}
							</button>
						</td>
						<td data-order="{Yii::$app->formatter->asTimestamp($user['loginDT'])}">{Yii::$app->formatter->asDatetime($user['loginDT'])}</td>
						<td data-order="{Yii::$app->formatter->asTimestamp($user['activeDT'])}">{Yii::$app->formatter->asDatetime($user['activeDT'])}</td>

						{if Yii::$app->user->can('admin')}
							<td>
								{if $user['id'] != Yii::$app->user->id}
									{Html::a(Html::fa('remove'),Url::toRoute(['user/delete', 'id' => $user['id']]), [ 'class' => 'btn btn-sm btn-danger', 'title' => Yii::tr('Remove user?', [], 'user'), 'data-toggle'=>'confirmation', 'data-btn-ok-class'=>'btn-xs btn-danger', 'data-title'=>Yii::tr('Remove user?', [], 'user'), 'data-btn-ok-label'=>Yii::tr('Yes', [], 'user'), 'data-btn-cancel-label'=>Yii::tr('No', [], 'user') ])}
								{/if}
							</td>
						{/if}
					</tr>
				{/foreach}
			</tbody>
		</table>
		{include file='pagination.tpl'}
	</div>
	<div role="tabpanel" class="tab-pane container" id="roles">
		<table class="table table-hover table-striped" id="role-list">
			<thead>
				<tr>
					<th>{Yii::tr('Name', [], 'user')}</th>
					<th>{Yii::tr('Description', [], 'user')}</th>
					<th>{Yii::tr('Rule name', [], 'user')}</th>
					<th>{Yii::tr('Data', [], 'user')}</th>
					<th>{Yii::tr('Child roles', [], 'user')}</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				{foreach $roles as $role}
					<tr>
						<td>{$role.name}</td>
						<td>{$role.description}</td>
						<td>{$role.ruleName}</td>
						<td>{$role.data}</td>
						<td>{foreach Yii::$app->authManager->getChildren($role.name) as $child}
								<p>{$child->name}</p>
							{/foreach}
						</td>
						<td>
							{if !$role.fixed}

								{Html::a(Html::fa('pencil'),'#roles',['class'=>'btn btn-xs btn-success role-edit', 'data-id'=>{$role.name} ])}
								{Html::a(Html::fa('remove'),{Url::toRoute(['rbac/delete-role','id'=>$role.name])},['class'=>'btn btn-xs btn-danger role-delete', 'data-id'=>{$role.name}, 'data-toggle'=>'confirmation', 'data-title'=>{Yii::tr('Remove?',[],'user')}, 'title'=>{Yii::tr('Remove?',[],'user')}, 'data-btn-ok-label'=>"{Yii::tr('Yes', [], 'user')}",'data-btn-ok-class'=>"btn btn-xs btn-danger", 'data-btn-cancel-label'=>"{Yii::tr('No', [], 'user')}"  ])}
							{/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		{Html::button({Yii::tr('Add', [], 'user')},['class'=>'btn btn-info role-add'])}
	</div>
	<div role="tabpanel" class="tab-pane container" id="permission">
		<table class="table table-hover table-striped" id="rule-list">
			<thead>
				<tr>
					<th>{Yii::tr('Name', [], 'user')}</th>
					<th>{Yii::tr('Description', [], 'user')}</th>
					<th>{Yii::tr('Rule name', [], 'user')}</th>
					<th>{Yii::tr('Data', [], 'user')}</th>
				</tr>
			</thead>
			<tbody>
				{foreach $permissions as $permission}
					<tr>
						<td>{$permission->name}</td>
						<td>{$permission->description}</td>
						<td>{$permission->ruleName}</td>
						<td>{$permission->data}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h3 class="modal-title" id="modalLabel"></h3>
			</div>
			<div class="modal-body">
				{Form assign='f' id="role-form" action="#roles" options=['data-pjax' => true]}
				{$f->field($roleForm, 'name')->textInput(['id'=>'name'])}
				{$f->field($roleForm, 'description')->textInput(['id'=>'description'])}
				{if count($rules)> 0}
					{$f->field($roleForm, 'ruleName')->select($rules, [ 'value' => 'name', 'label' => 'name' ], [ 'class' => 'selectpicker', 'title' => Yii::tr('Select rule', [], 'user'),'id'=>'ruleName'])}
				{/if}
				{$f->field($roleForm, 'data')->textInput(['id'=>'data'])}
				{$f->field($roleForm, 'childRoles')->select(Yii::$app->authManager->getRoles(), [ 'value' => 'name', 'label' => 'name' ], [ 'class' => 'selectpicker', 'multiple' => true, 'title' => Yii::tr('Select child roles', [], 'user'),'id'=>'childRoles' ])}
				{$f->field($roleForm, 'childPermissions')->select($permissions, [ 'value' => 'name', 'label' => 'name' ], [ 'class' => 'selectpicker', 'multiple' => true, 'title' => Yii::tr('Select permissions', [], 'user'),'id'=>'childPermissions' ])}
				{$f->field($roleForm, 'method')->hidden(['id'=>'method', 'label' => false])}
				{/Form}
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary role-form">{Yii::tr('Save changes', [], 'user')}</button>
			</div>
		</div>
	</div>
</div>
{if count($roleForm->errors)>0}
	<script type="text/javascript">
		$('#formModal').modal('show');
	</script>
{/if}
<script type="text/javascript">
	var childRoles = $('#childRoles');
	childRoles.find('[value=admin]').remove();
	childRoles.selectpicker('refresh');
	var roles = {
		data      : {json_encode($roles)},
		getByName : function (name) {
			for (var i in this.data) {
				if (this.data[ i ].name == name)
					return this.data[ i ];
			}
			return null;
		}
	};
	$().ready(function () {
		var originalValue = $('#search-user').val();
		$('#search-user').val('');
		$('#search-user').blur().focus().val(originalValue);
	});
	var loadTimeout = null;
	$(document).on('input', '#search-user', function (e) {
		if (e.keyCode != 9) {
			if (loadTimeout !== null) {
				clearTimeout(loadTimeout);
				loadTimeout = null;
			}
			var el      = $(this);
			var role    = $('#select-filter-role');
			loadTimeout = setTimeout(function () {
				window.location.href = '{Url::toRoute('user/manage')}?search=' + el.val() + '&filterRole=' + role.val();

				loadTimeout = null;
			}, 1000);
		}

	});
	$('.select-filter-role').change(function () {
		console.log($(this).val());
		window.location.href = '{Url::toRoute('user/manage')}?filterRole=' + $(this).val() + '&search=' + $('#search-user').val();
	});
	$('.role-add').click(function (e) {
		$('#modalLabel').html('{Yii::tr('Adding Role', [], 'user')}');
		$('#method').val('rbac-add');
		$('#name').val('');
		$('#ruleName').val('');
		$('#data').val('');
		$('#description').val('');
		$('#formModal').modal('show');
		childRoles.selectpicker('val', 0);
		$('#childPermissions').selectpicker('val', 0);
	});
	$('.role-edit').click(function (e) {
		e.preventDefault();
		e.stopPropagation();
		setEditData($(this).data('id'));
		$('#formModal').modal('show');
	});

	function setEditData (roleName) {
		var role = roles.getByName(roleName);
		if (role !== null) {
			$('#method').val(role.name);
			$('#modalLabel').html('{Yii::tr('Editing a Role', [], 'user')}');
			$('#name').val(role.name);
			$('#description').val(role.description);
			$('.form-group').removeClass('has-error');
			$('.error-block').addClass('hide');
			$('#ruleName').val(role.ruleName);
			$('#data').val(role.description);
			childRoles.find('[value=' + role.name + ']').remove();
			childRoles.selectpicker('val', role.childRoles);
			childRoles.selectpicker('refresh');
			$('#childPermissions').selectpicker('val', role.childPermissions);
		}
	}

	$('.role-form').click(function () {
		$('#role-form').submit();
	});
	$('.select-user-role').change(function () {
		jQuery.ajax({
			url      : '{Url::toRoute(['rbac/user-role'])}',
			type     : 'POST',
			data     : { _csrf : '{Yii::$app->request->getCsrfToken()}', id : $(this).data('id'), roles : $(this).val() },
			dataType : "json",
		});
	});
	$('.user-state').click(function () {
		var id     = $(this).data('id');
		var button = $('#btn' + id);
		jQuery.ajax({
			url      : '{Url::toRoute(['rbac/user-state'])}',
			type     : 'POST',
			data     : { _csrf : '{Yii::$app->request->getCsrfToken()}', id : id, state : $(this).data('state') },
			dataType : "json",
			success  : function (data) {
				if (data == 1) {
					button.html('{Html::fa('check',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Disable', [], 'user')])}').attr('title', '{Yii::tr('Disable', [], 'user')}').removeClass('btn-danger').data('state', 0).addClass('btn-success');
				}
				else {
					button.html('{Html::fa('ban',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Enable', [], 'user')])}').attr('title', '{Yii::tr('Enable', [], 'user')}').removeClass('btn-success').data('state', 1).addClass('btn-danger');
				}
			}
		});
	});
</script>