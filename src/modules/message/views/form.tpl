`
<div class="content">
	<div class="col-md-12">
		{Form assign="f" id="create-form" upload=true}
		{if $model->isNewRecord}
			{$f->field($model,'category')->label(Yii::t('message','Category'))->textInput()}
			{$f->field($model,'message')->label(Yii::t('message','Message'))->textInput()}
		{else}
			<p>{$model->message}</p>
		{/if}
		{$f->field($model,'description')->textarea()}
		{foreach Yii::$app->getModule('messages')->languages as $one}
			{$f->field($model,"languages[{$one}]")->label($one)->textarea(['rows' => 1])}
		{/foreach}
			<div class="form-group">
				{Html::submitButton(Yii::tr('Save', [], 'page'), ['class' => 'btn btn-success'])}
				{Html::a(Yii::tr('Cancel', [], 'menu'),Yii::$app->request->referrer,['class'=>'btn btn-danger'])}
			</div>
			<input type="hidden" id="{Yii::$app->request->csrfParam}" name="{Yii::$app->request->csrfParam}" value="{Yii::$app->request->csrfToken}">
		{/Form}
	</div>
</div>
