<footer>
	<div class="container{if $fluid}-fluid{/if}">
		<div class="row">
			<div class="col-sm-3 footer-copyright">
				&copy; {if date('Y')>$copyrightFrom}{$copyrightFrom}-{date('Y')}{else}{date('Y')}{/if}
				{if !empty($company['url'])}
					{Html::a( $company['title'], $company['url'])}
				{else}
					{$company['title']}
				{/if}
			</div>
			<div class="col-sm-6 footer-links">
				{if !empty($links)}
					{foreach $links as $link}
						{Html::a( $link['title'], $link['url'])}
					{/foreach}
				{/if}
			</div>
			<div class="col-sm-3 footer-version">
				{Yii::tr('Version {version}', ['version' => Yii::$app->version], 'widgets/footer')}
			</div>
		</div>
	</div>
</footer>