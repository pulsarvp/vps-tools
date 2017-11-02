<div class="content">
	<div class="col-md-12">
		{Form assign="f" id="create-form" upload=true}
		{$f->field($model,'guid')->textInput()}
		{$f->field($model,'title')->textInput()}
		{$f->field($model,'text')->textarea(['class' => 'textarea'])}
		{if isset($menudrop)}
			{$f->field($model,'menus')->label(Yii::tr('Menu', [], 'page'))->dropDownList($menudrop,['class'=>'selectpicker','multiple'=>true])}
			<div class="update_url">
				{$f->field($model,'updateUrl')->checkbox()}
			</div>
		{/if}
			<div class="form-group">
				<div class="col-md-9 col-md-offset-3">
					{Html::submitButton(Yii::tr('Save', [], 'page'), ['class' => 'btn btn-success'])}
					{Html::a(Yii::tr('Cancel', [], 'menu'),Yii::$app->request->referrer,['class'=>'btn btn-danger'])}
				</div>
			</div>
		{/Form}
	</div>
</div>
<script>
	$().ready(function () {
		if ($('#page-menus').val() == null)
			$('.update_url').addClass('hide');
		$('#page-menus').on('change', function () {
			if ($(this).val() != null)
				$('.update_url').removeClass('hide');
			else
				$('.update_url').addClass('hide');
		});

		$('.textarea').redactor({
			minHeight                : 300,
			replaceDivs              : false,
			deniedTags               : [ 'script' ],
			removeComments           : true,
			buttons                  : [ 'bold', 'italic', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'lists', 'link', 'image' ],
			imageUpload              : '{Url::toRoute(["/page/image"])}',
			imageEditable            : true,
			imageResizable           : true,
			uploadFileFields         : { '{Yii::$app->request->csrfParam}' : '{Yii::$app->request->csrfToken}' },
			uploadImageFields        : { '{Yii::$app->request->csrfParam}' : '{Yii::$app->request->csrfToken}' },
			imageUploadErrorCallback : function (json) {
				if (json.message)
					alert(json.message);
			},
			uploadStartCallback      : function (e) {
				if (e.dataTransfer)
					var fileElement = e.dataTransfer.files;
				else
					var fileElement = $("input[type='file']").prop('files');
				if (fileElement[ 0 ]) {
					var fileSize = fileElement[ 0 ].size;
					if (fileSize > {HumanHelper::maxBytesUpload()}) {
						alert('{Yii::tr('Image size exceeds {max}.', [ 'max' => HumanHelper::maxUpload() ], 'page')}');
						this.progress.hide();
						throw 'stop';
					}
				}
			}
		});
	});
</script>