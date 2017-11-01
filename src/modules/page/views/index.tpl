{Html::a(Yii::tr('Add', [], 'page'), Url::toRoute(['/pages/page/add']),[ 'class' => 'btn btn-primary'])}
<table class="table table-bordered table-hover" id="page-list">
	<thead>
		<tr>
			{foreach [ 'id', 'guid', 'title', 'active', 'dt']  as $key}
				<th>
					{Yii::tr(ucfirst($key), [], 'page')}
					{if isset($sort)}
						{if array_key_exists($key, $sort->attributeOrders)}
							{if $sort->attributeOrders[$key] == SORT_ASC}
								<a href="{Url::current([ 'sort' => "-`$key`",'page' => "" ])}">
									<i class="fa fa-sort-numeric-asc"></i>
								</a>
							{else}
								<a href="{Url::current([ 'sort' => $key,'page' => "" ])}">
									<i class="fa fa-sort-numeric-desc"></i></a>
							{/if}
						{elseif array_key_exists($key, $sort->attributes)}
							<a href="{Url::current([ 'sort' => $key,'page' => "" ])}"><i class="fa fa-sort"></i></a>
						{/if}
					{/if}
				</th>
			{/foreach}
			<th></th>
		</tr>

	</thead>
	<tbody>
		{foreach $models as $model}
			<tr>
				<td>{Html::a($model->id, Url::toRoute(['/pages/page/view', 'id' => $model->id]), ['title' => $model->title ])}</td>
				<td>{Html::a($model->guid, Url::toRoute(['/pages/page/view', 'id' => $model->id]), ['title' => $model->guid ])}</td>
				<td>{Html::a($model->title, Url::toRoute(['/pages/page/view', 'id' => $model->id]), ['title' => $model->title ])}</td>
				<td>
					{if $model->active}
						{Html::fa('check',['class'=>'text-success'])}
					{/if}
				</td>
				<td>{Yii::$app->formatter->asDatetime($model->dt)}</td>
				<td>
					{Html::a(Html::fa('pencil'),Url::toRoute(['/pages/page/edit', 'id' => $model->id]), [ 'class' => 'btn btn-xs btn-success', 'title' => $model->title ])}
					{Html::a(Html::fa('remove'),Url::toRoute(['/pages/page/delete', 'id' => $model->id]), [ 'class' => 'btn btn-xs btn-danger', 'title' => Yii::tr('Remove page?', [], 'page'), 'data-toggle'=>'confirmation', 'data-btn-ok-class'=>'btn-xs btn-danger', 'data-title'=>Yii::tr('Remove page?', [], 'page'), 'data-btn-ok-label'=>Yii::tr('Yes', [], 'page'), 'data-btn-cancel-label'=>Yii::tr('No', [], 'page') ])}
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>
{include file='@pageViews/../../views/pagination.tpl'}