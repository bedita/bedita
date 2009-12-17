{$javascript->link("jquery/jquery.treeview", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.changealert", false)}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#system_events";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="systemEvents"}

{include file="inc/menucommands.tpl" method="systemEvents" fixed=true}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2>{t}System events{/t}</h2>
	{include file="./inc/toolbar.tpl" label_items='events'}
	</div>
</div>

<div class="mainfull">
	
	{include file="inc/form_events.tpl"}

</div>