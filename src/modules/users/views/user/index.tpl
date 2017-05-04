<div>
	<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h3 class="panel-title">{$user->name}</h3>
			</div>
			<div class="panel-body">
				<div class="row">
					<div class=" col-md-8 col-lg-8 ">
						<table class="table table-user-information">
							<tbody>
								<tr>
									<td>{Yii::tr('Email')}</td>
									<td>{Html::mailto({$user->email})}</td>
								</tr>
								<tr>
									<td>{Yii::tr('Login DT')}</td>
									<td>{Yii::$app->formatter->asDate($user->loginDT,"dd MMMM yyyy HH:mm")}</td>
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
