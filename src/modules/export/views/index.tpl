{if Yii::$app->user->identity->isPermission('viewExportList')}
	{if Yii::$app->user->identity->isPermission('createExport')}
		<div class="content margin-bottom">
			{Html::a(Yii::tr('Create', [], 'export'), Url::toRoute(['export/create']),[ 'class' => 'btn btn-primary'])}
		</div>
	{/if}
	<table class="table table-bordered table-hover" id="export-list">
		<thead>
			<tr>
				{foreach [ 'id', 'title','prefix', 'createDT', 'dt']  as $key}
					<th>
						{Yii::tr(ucfirst($key), [], 'export')}
					</th>
				{/foreach}
				{if Yii::$app->user->identity->isPermission(['editExport','deleteExport'])}
					<th></th>
				{/if}
				{if Yii::$app->user->identity->isPermission('generateExport')}
					<th></th>
				{/if}
			</tr>
		</thead>
		<tbody>
			{foreach $models as $k=>$model}
				<tr data-id="{$model->id}">
					{if Yii::$app->user->identity->isPermission('viewExport')}
						<td>{Html::a($model->id, Url::toRoute(['export/view', 'id' => $model->id]), ['title' => $model->title ])}</td>
						<td>{Html::a($model->title, Url::toRoute(['export/view', 'id' => $model->id]), ['title' => $model->title ])}</td>
					{else}
						<td>{$model->id}</td>
						<td>{$model->title}</td>
					{/if}
					<td>{$model->prefix}</td>
					<td>{Yii::$app->formatter->asDateTime($model->createDT)}</td>
					<td>{Yii::$app->formatter->asDateTime($model->dt)}</td>
					{if Yii::$app->user->identity->isPermission(['editExport','deleteExport'])}
						<td>
							{if Yii::$app->user->identity->isPermission('editExport')}
								{Html::a(Html::fa('pencil'),Url::toRoute(['export/edit', 'id' => $model->id]), [ 'class' => 'btn btn-xs btn-success', 'title' => $model->title ])}
							{/if}
							{if Yii::$app->user->identity->isPermission('deleteExport')}
								{Html::a(Html::fa('remove'),Url::toRoute(['export/delete', 'id' => $model->id]), [ 'class' => 'btn btn-xs btn-danger', 'title' => Yii::tr('Remove export?', [], 'export'), 'data-toggle'=>'confirmation', 'data-btn-ok-class'=>'btn-xs btn-danger', 'data-title'=>Yii::tr('Remove export?', [], 'export'), 'data-btn-ok-label'=>Yii::tr('Yes'), 'data-btn-cancel-label'=>Yii::tr('No') ])}
							{/if}
						</td>
					{/if}
					{if Yii::$app->user->identity->isPermission(['generateExport'])}
						<td>

							{Html::a(Yii::tr('Generate', [], 'export'),Url::toRoute(['export/generate', 'id' => $model->id]), [ 'class' => 'btn btn-xs btn-success', 'title' => $model->title ])}

						</td>
					{/if}
				</tr>
			{/foreach}
		</tbody>
	</table>
	{include file='@vpsViews/pagination.tpl'}
{else}
	<div class="text-danger">{Yii::tr('Попытка взлома detected!')}</div>
{/if}