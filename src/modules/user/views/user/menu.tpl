<ul class="nav navbar-nav nav-user pull-right">
	{if Yii::$app->user->isGuest}
		<li>{Html::a(Yii::tr('Login', [], 'user'), $loginUrl)}</li>
	{else}
		{if Yii::$app->user->identity->image}
			<li>
				{if $useUserLink}
					{Html::a(Html::img(Yii::$app->user->identity->image),Url::toRoute(['/user']), [ 'title' => Yii::$app->user->identity->name ,'class'=>'navbar-brand nav-user-image'])}
				{else}
					{Html::a(Html::img(Yii::$app->user->identity->image),null, [ 'title' => Yii::$app->user->identity->name ,'class'=>'navbar-brand nav-user-image'])}
				{/if}
			</li>
		{/if}

		{if $useUserLink}
			<li><a href="{Url::toRoute(['/user'])}">{Yii::$app->user->identity->name}</a></li>
		{else}
			<li class="navbar-text">{Yii::$app->user->identity->name}</li>
		{/if}
		<li>
			{Html::a(Html::fa('sign-out', [ 'title' => Yii::tr('Sign out') ]), ['/user/logout'], [ 'title' => Yii::tr('Sign out', [], 'user') ])}
		</li>
	{/if}
</ul>
