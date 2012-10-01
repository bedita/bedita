{$this->Html->script("jquery/jquery.treeview", false)}
{$this->Html->script("form", false)}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#system_info";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="systemInfo"}

{include file="inc/menucommands.tpl" method="systemInfo" fixed=true}

<div class="mainfull">
	
	{include file="inc/form_info.tpl" method="systemInfo"}

</div>
