{if Yii::$app->user->identity->hasPermission(['admin_page'])}
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
					{Html::submitButton(Yii::tr('Save', [], 'page'), ['class' => 'btn btn-success'])}
					{Html::a(Yii::tr('Cancel', [], 'menu'),Yii::$app->request->referrer,['class'=>'btn btn-danger'])}
				</div>
				<input type="hidden" id="{Yii::$app->request->csrfParam}" name="{Yii::$app->request->csrfParam}" value="{Yii::$app->request->csrfToken}">
			{/Form}
		</div>
	</div>
	<script>
		$().ready(function () {
			if ($('#page-menus').val() == null)
				$('.update_url').addClass('d-none');
			$('#page-menus').on('change', function () {
				if ($(this).val() != null)
					$('.update_url').removeClass('d-none');
				else
					$('.update_url').addClass('d-none');
			});

			function showError (error) {
				$('.redactor-box').parent().find('.error-block').remove();
				$('.field-page-text').addClass('has-error');
				$('.redactor-box').parent().append('<div class="help-block help-block-error error-block"><ul class="list-unstyled"><li>' + error + '</li></ul></div>');
			}

			function hideError (error) {
				$('.redactor-box').parent().find('.error-block').remove();
				$('.field-page-text').removeClass('has-error');
			}

			$('.textarea').redactor({
				minHeight         : 300,
				script            : false,
				buttons           : [ 'bold', 'italic', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'lists', 'link', 'image' ],
				imageUpload       : '{Url::toRoute(["/page/image"])}',
				fileUpload        : '{Url::toRoute(["/page/image"])}',
				imageEditable     : true,
				imageResizable    : true,
				fileUploadFields  : '#{Yii::$app->request->csrfParam}',
				imageUploadFields : '#{Yii::$app->request->csrfParam}',
				lang			  : 'ru',
				callbacks         : {
					change           : function () {
						hideError();
					},
					click            : function () {
						hideError();
					},
					modalOpened      : function () {
						hideError();
					},
					fileUploadError  : function (json) {
						if (json.message) {
							showError(json.message)
						}
					},
					imageUploadError : function (json) {
						if (json.message) {
							showError(json.message)
						}
					},
					uploadStart      : function (e) {
						if (e.dataTransfer)
							var fileElement = e.dataTransfer.files;
						else
							var fileElement = $("input[type='file']").prop('files');
						if (fileElement[ 0 ]) {
							var fileSize = fileElement[ 0 ].size;
							if (fileSize > {HumanHelper::maxBytesUpload()}) {
								showError('{Yii::tr('Image size exceeds {max}.', [ 'max' => HumanHelper::maxUpload() ], 'page')}');
								return false;
							}
						}
					}
				}
			});
		});
	</script>
{else}
	<div class="text-danger">{Yii::tr('You have no permission to access this page.')}</div>
{/if}