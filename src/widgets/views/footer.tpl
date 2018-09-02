<footer class="mt-4">
	<div class="container{if $fluid}-fluid{/if}">
		<div class="row">
			<div class="col-sm-3 footer-copyright">
				{assign year date('Y')}
				&copy; {if $copyrightFrom and $year>$copyrightFrom}{$copyrightFrom}-{/if}{$year}
				{if !empty($company['url'])}
					{Html::a( $company['title'], $company['url'])}
				{else}
					{$company['title']}
				{/if}
			</div>
			<div class="col-sm-6 footer-links text-center">
				{if !empty($links)}
					{foreach $links as $link}
						{Html::a( $link['title'], $link['url'], [ 'class' => 'text-nowrap ml-2 mr-2' ])}
					{/foreach}
				{/if}
			</div>
			{if $showVersion}
				<div class="col-sm-3 footer-version text-right">
					{Yii::tr('Version {version}', ['version' => Yii::$app->version], 'widgets/footer')}
				</div>
			{/if}
		</div>
	</div>
</footer>