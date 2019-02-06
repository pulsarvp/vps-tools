{if Yii::$app->user->identity->isPermission(['createExport','editExport'])}
	<div class="content">
		<div class="col-sm-8">
			{Form assign="f" id="create-form"}
			{$f->field($model,'title')->textInput()->label(Yii::tr('Title', [], 'export'))}
			{$f->field($model,'description')->textarea(['class' => 'redactor'])->label(Yii::tr('Description', [], 'export'))}
			{$f->field($model,'query')->textarea()->label(Yii::tr('Query', [], 'export'))}
			{$f->field($model,'prefix')->textInput()->label(Yii::tr('Prefix', [], 'export'))}
		</div>
		{Html::submitButton(Yii::tr('Save', [], 'export'), ['class' => 'btn btn-success btn-block'])}
		{/Form}
	</div>
{else}
	<div class="text-danger">{Yii::tr('Попытка взлома detected!')}</div>
{/if}