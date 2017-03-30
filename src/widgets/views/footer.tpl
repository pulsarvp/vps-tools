<footer>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-3">
				&copy; {if date('Y')>$copyrightFrom}{$copyrightFrom}-{date('Y')}{else}{date('Y')}{/if}
				{if !empty($company['url'])}
					{Html::a( $company['title'], $company['url'])}
				{else}
					{$company['title']}
				{/if}
			</div>
			<div class="col-md-9">
				{if !empty($links)}
					{foreach $links as $link}
						{Html::a( $link['title'], $link['url'])}
					{/foreach}
				{/if}
			</div>
		</div>
	</div>
</footer>