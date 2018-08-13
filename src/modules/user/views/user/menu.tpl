{if !empty(Yii::$app->user->identity->image)}
	{if $useUserLink}
		{Html::a(Html::img(Yii::$app->user->identity->image),Url::toRoute(['/user']), [ 'title' => Yii::$app->user->identity->name ,'class'=>'navbar-brand nav-user-image'])}
	{else}
		{Html::a(Html::img(Yii::$app->user->identity->image),null, [ 'title' => Yii::$app->user->identity->name ,'class'=>'navbar-brand nav-user-image'])}
	{/if}
{/if}
<ul class="navbar-nav justify-content-end">
	{if Yii::$app->user->isGuest}
		<li class="nav-item">{Html::a(Yii::tr('Login', [], 'user'), $loginUrl)}</li>
	{else}
		{if $useUserLink}
			<li class="nav-item">
				<a href="{Url::toRoute(['/user'])}" class="nav-link">{Yii::$app->user->identity->name}</a></li>
		{else}
			<li class="nav-item">
				<span class="navbar-text">{Yii::$app->user->identity->name}</span>
			</li>
		{/if}
		<li class="nav-item">
			{Html::a(Html::fa('sign-out', [ 'title' => Yii::tr('Sign out') ]), ['/user/logout'], [ 'title' => Yii::tr('Sign out', [], 'user'),'class'=>'nav-link' ])}
		</li>
	{/if}
</ul>