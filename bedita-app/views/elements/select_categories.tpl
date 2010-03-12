{if !empty($categories)}
<ul class="menuleft insidecol">
	<li><a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a></li>
	<ul id="groups" {if (empty($categorySearched))}style="display:none"{/if}>
		{foreach key=cat_id item=cat_label from=$categories}
		<li {if (($categorySearched|default:'')==$cat_id)}class="on"{/if}><a href="{$html->url('/')}{$currentModule.path}/index/category:{$cat_id}">{$cat_label}</a></li>
		{/foreach}
	</ul>
</ul>
{/if}