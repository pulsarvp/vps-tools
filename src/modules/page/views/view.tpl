{if isset($page)}
	<div class="container">
	<h1>
		{$page->title}
	</h1>
	<span class="text-center">
			{if !$page->active}
				{Html::button(Yii::tr('Activate', [], 'page'), [ 'data-id'=>$page->id, 'class' => 'btn btn-success page-active'])}
			{else}
				{Html::button(Yii::tr('Deactivate', [], 'page'), [ 'data-id'=>$page->id, 'class' => 'btn btn-danger page-active'])}
			{/if}
		</span>
	{Html::a(Yii::tr('Edit', [], 'page'), Url::toRoute(['/pages/page/edit', 'id' => $page->id]),[ 'class' => 'btn btn-primary'])}
	<div class="guid">{$page->guid}</div>
	<div class="date">{Yii::$app->formatter->asDatetime($page->dt)}</div>
	<div class="text">{$page->text}</div>
	{if useMenu and count($page->menu) > 0}
		<p>{Yii::tr('Menu',[],'page')}</p>
		{foreach $page->menu as $menu}
			<a href="{$menu->url}" target="_blank">{Yii::tr($menu->title)}</a>
		{/foreach}
		</div>
		</div>
	{/if}
	</div>
{/if}
<script>
	$(document).on('click', '.page-active', function () {
		var id  = $(this).data('id');
		var div = $(this).parent();
		jQuery.ajax({
			url      : "{Url::toRoute(['page/activate'])}",
			type     : 'POST',
			data     : { "{Yii::$app->request->csrfParam}" : '{Yii::$app->request->getCsrfToken()}', id : id },
			dataType : "json",
			success  : function (data) {
				if (data) {
					div.html('{Html::button(Yii::tr("Deactivate", [], "page"), [ "data-id"=>$page->id, "class" => "btn btn-danger page-active"])}');
				}
				else {
					div.html('{Html::button(Yii::tr("Activate", [], "page"), [ "data-id"=>$page->id, "class" => "btn btn-success page-active"])}');
				}
			}
		});
	});
</script>
