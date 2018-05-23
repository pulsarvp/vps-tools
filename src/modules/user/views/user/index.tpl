<div class="container">
	<div class="row justify-content-md-center">
		<div class="col-3 col-lg-6">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title">{$user->name}</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<table class="table table-user-information">
								<tbody>
									<tr>
										<td>{Yii::tr('Email', [], 'user')}</td>
										<td>{Html::mailto({$user->email})}</td>
									</tr>
									<tr>
										<td>{Yii::tr('Login DT', [], 'user')}</td>
										<td>{Yii::$app->formatter->asDatetime($user->loginDT)}</td>
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
</div>
