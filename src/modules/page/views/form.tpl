<div class="content">
	<div class="col-md-8">
		{Form assign="f" id="create-form" upload=true}
		{$f->field($model,'guid')->textInput()}
		{$f->field($model,'title')->textInput()}
		{$f->field($model,'text')->textarea(['class' => 'textarea'])}
		{if isset($menudrop)}
			{$f->field($model,'menus')->label(Yii::tr('Menu', [], 'page'))->dropDownList($menudrop,['class'=>'selectpicker','multiple'=>true])}
			<div class="update_url hide">
				{$f->field($model,'updateUrl')->checkbox()}
			</div>
		{/if}
	</div>
	{Html::submitButton(Yii::tr('Save', [], 'page'), ['class' => 'btn btn-success btn-block'])}
	{/Form}
</div>
<script>
	$().ready(function () {
		$('#page-menus').on('change', function () {
			$('.update_url').removeClass('hide');
		});
		$('.textarea').redactor({
			minHeight : 300,
			buttons   : [ 'bold', 'italic', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'lists', 'link' ]
		});
	});
</script>