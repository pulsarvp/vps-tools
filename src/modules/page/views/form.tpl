<div class="content">
	<div class="col-md-8">
		{Form assign="f" id="create-form" upload=true}
		{$f->field($model,'guid')->textInput()}
		{$f->field($model,'title')->textInput()}
		{$f->field($model,'text')->textarea(['class' => 'redactor'])}
		{if isset($menudrop)}
			{$f->field($model,'menus')->label(Yii::tr('Menu', [], 'page'))->dropDownList($menudrop,['class'=>'selectpicker','multiple'=>true])}
		{/if}
	</div>
	{Html::submitButton(Yii::tr('Save', [], 'page'), ['class' => 'btn btn-success btn-block'])}
	{/Form}
</div>
<script>
	$().ready(function () {
		$('#redactor').redactor({
			minHeight : 300,
			buttons   : [ 'html', 'bold', 'italic', 'lists', 'link' ]
		});
	});
</script>