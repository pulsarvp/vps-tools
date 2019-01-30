{if isset($model)}
	{Html::a(Yii::tr('Edit', [], 'page'), Url::toRoute(['/messages/message/update', 'id' => $model->id]),[ 'class' => 'btn btn-primary'])}
	<dl class="dl-horizontal">
		<dt>{Yii::tr('ID')}</dt>
		<dd>{$model->id}</dd>
		<dt>{Yii::t('message','Category')}</dt>
		<dd>{$model->category}</dd>
		<dt>{Yii::t('message','Message')}</dt>
		<dd>{$model->message}</dd>
		<dt>{Yii::t('message','Description')}</dt>
		<dd>{$model->description}</dd>
		{foreach $model->languages as $key=>$item}
			<dt>{Yii::tr($key)}</dt>
			<dd>{$item}</dd>
		{/foreach}
	</dl>
{/if}
