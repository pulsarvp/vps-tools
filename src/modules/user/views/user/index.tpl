<div class="row user-infos">
	<div class="col-xs-12 col-sm-12 col-md-10 col-lg-10 col-xs-offset-0 col-sm-offset-0 col-md-offset-1 col-lg-offset-1">
		<div class="panel panel-primary">
			<div class="panel-heading">
			</div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-3 col-lg-3 hidden-xs hidden-sm">
						{Html::img($user->image,[ 'class'=>'img-circle','width'=>'200'])}
					</div>
					<div class=" col-md-9 col-lg-9 hidden-xs hidden-sm">
						<strong>{$user->name}</strong><br>
						<table class="table table-user-information">
							<tbody>
								<tr>
									<td>{Yii::tr('ID', [], 'user')}:</td>
									<td>{$user->id}</td>
								</tr>
								<tr>
									<td>{Yii::tr('Name', [], 'user')}:</td>
									<td>{$user->name}</td>
								</tr>
								<tr>
									<td>{Yii::tr('Profile', [], 'user')}:</td>
									<td>{$user->profile}</td>
								</tr>
								<tr>
									<td>{Yii::tr('Email', [], 'user')}:</td>
									<td>{Html::mailto($user->email)}</td>
								</tr>
								<tr>
									<td>{Yii::tr('Active', [], 'user')}:</td>
									<td>{if $user['active']}
											{Html::fa('check',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Disable', [], 'user')])}
										{else}
											{Html::fa('ban',['id'=>"btn{$user['id']}",'class'=>'text-default','title'=>Yii::tr('Enable', [], 'user')])}
										{/if}
									</td>
								</tr>
								<tr>
									<td>{Yii::tr('Login Dt', [], 'user')}:</td>
									<td>{Yii::$app->formatter->asDatetime($user->loginDT)}</td>
								</tr>
								<tr>
									<td>{Yii::tr('ActiveDT', [], 'user')}:</td>
									<td>{Yii::$app->formatter->asDatetime($user->activeDT)}</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="panel-footer">
			</div>
		</div>
	</div>
</div>