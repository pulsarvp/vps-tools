<div class="row">
	<div class="col-md-8 col-md-offset-2">
		{foreach Yii::$app->notification->data as $d}
			<div class="alert alert-{$d->class}">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$d->message}
			</div>
		{/foreach}
	</div>
</div>