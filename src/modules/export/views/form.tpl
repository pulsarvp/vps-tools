{if Yii::$app->user->identity->hasPermission(['createExport','editExport'])}
	<div class="row">
		<div class="col-12 col-md-8">
			{Form assign="f" id="create-form"}
			{$f->field($model,'title')->textInput()->label(Yii::tr('Title', [], 'export'))}
			{$f->field($model,'description')->textarea(['class' => 'redactor'])->label(Yii::tr('Description', [], 'export'))}
			{$f->field($model,'query')->textarea()->label(Yii::tr('Query', [], 'export'))}
			{$f->field($model,'prefix')->textInput()->label(Yii::tr('Prefix', [], 'export'))}
			{Html::submitButton(Yii::tr('Save', [], 'export'), ['class' => 'btn btn-success btn-block'])}
		</div>
		{/Form}
	</div>
{else}
	<div class="text-danger">{Yii::tr('You have no permission to access this page.')}</div>
{/if}