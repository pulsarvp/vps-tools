{if Yii::$app->user->identity->hasPermission('viewExport')}
	<div class="row">
		<div class="col-12 mb-4">
			{if Yii::$app->user->identity->hasPermission('editExport')}
				{Html::a(Html::fa('pencil'),Url::toRoute(['export/edit', 'id' => $export->id]), [ 'class' => 'btn btn-success mr-2 mb-2', 'title' => $export->title ])}
			{/if}
			{if Yii::$app->user->identity->hasPermission('generateExport')}
				{Html::a(Yii::tr('Generate XLS', [], 'export'),Url::toRoute(['export/generate-xls', 'id' => $export->id]), [ 'class' => 'btn btn-info mr-2 mb-2', 'title' => $export->title ])}
				{Html::a(Yii::tr('Generate CSV', [], 'export'),Url::toRoute(['export/generate', 'id' => $export->id]), [ 'class' => 'btn btn-info mb-2', 'title' => $export->title ])}
			{/if}
		</div>
		<div class="col-12 mb-4">
			<dl>
				<dt>{Yii::tr('ID', [], 'export')}</dt>
				<dd>{$export->id}</dd>
				<dt>{Yii::tr('Title', [], 'export')}</dt>
				<dd>{$export->title}</dd>
				<dt>{Yii::tr('Description', [], 'export')}</dt>
				<dd>{$export->description}</dd>
				<dt>{Yii::tr('Query', [], 'export')}</dt>
				<dd>
					<pre>{$export->query}</pre>
				</dd>
				<dt>{Yii::tr('Prefix', [], 'export')}</dt>
				<dd>{$export->prefix}</dd>
				<dt>{Yii::tr('CreateDT', [], 'export')}</dt>
				<dd>{Yii::$app->formatter->asDateTime($export->createDT)}</dd>
				<dt>{Yii::tr('Dt', [], 'export')}</dt>
				<dd>{Yii::$app->formatter->asDateTime($export->dt)}</dd>
			</dl>
		</div>
		{if isset($models) and count($models)>0}
		<div class="col-12">
			<div class="overflow-scroll mb-4">
				<table class="table table-bordered table-hover" id="export-list">
					<thead>
						<tr>
							{foreach  array_keys(current($models)) as $key}
								<th>
									{Yii::tr(ucfirst($key))}
								</th>
							{/foreach}
						</tr>
					</thead>
					<tbody>
						{foreach $models as $k=>$model}
							<tr>
								{foreach $model as $item}
									<td>{$item}</td>
								{/foreach}
							</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			{include file='pagination.tpl'}
		</div>
		{/if}
	</div>
{else}
	<div class="text-danger">{Yii::tr('You have no permission to access this page.')}</div>
{/if}