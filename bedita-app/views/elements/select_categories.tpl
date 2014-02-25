{if !empty($categories)}
	<li>
		<a href="javascript:void(0)" onClick="$('#groups').slideToggle();">{t}Select by category{/t}</a>
	
		<ul id="groups" style="width:240px; padding:5px 0 0 0; {if !$view->SessionFilter->check('category')}display:none{/if}">
			{foreach key=cat_id item=cat_label from=$categories}
			<li {if (($view->SessionFilter->read('category')|default:'')==$cat_id)}class="on"{/if}>
			<a href="{$html->url('/')}{$currentModule.url}/{$view->action}/category:{$cat_id}">{$cat_label}</a></li>
			{/foreach}
			<li>
				<a href="{$html->url('/')}{$currentModule.url}/{$view->action}/cleanFilter:1">{t}see all{/t}</a>
			</li>
		</ul>
	
	</li>
{/if}