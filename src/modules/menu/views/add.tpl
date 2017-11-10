{Form assign='f' id='form-new-menu'}
{$f->field($model, 'title')->label(Yii::tr('Title', [], 'menu'))->textInput()}
{$f->field($model, 'path')->label(Yii::tr('Path', [], 'menu'))->textInput()}
{$f->field($model, 'url')->label(Yii::tr('Url', [], 'menu'))->textInput()}
	<div class="form-group">
		<div class="col-md-10 col-md-offset-2">
			{Html::a(Yii::tr('Cancel', [], 'menu'),Yii::$app->request->referrer,['class'=>'btn btn-danger'])}
			<button class="btn btn-primary" type="submit" id="s-save">{Yii::tr('Save', [], 'menu')}</button>
		</div>
	</div>
{/Form}