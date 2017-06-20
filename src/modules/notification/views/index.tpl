{if count(Yii::$app->notification->data) > 0}
	<div class="noitifcation-container">
		{foreach Yii::$app->notification->data as $d}
			<div class="notification alert alert-{$d->class}">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				{$d->message}
			</div>
		{/foreach}
	</div>
{/if}