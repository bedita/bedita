{$html->script("jquery/jquery.treeview", false)}
{$html->script("form", false)}

{literal}
<script type="text/javascript">
	$(document).ready(function(){
		var openAtStart ="#email_info";
		$(openAtStart).prev(".tab").BEtabstoggle();
	});
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="emailInfo"}

{include file="inc/menucommands.tpl" method="emailInfo" fixed=true}

<div class="mainfull">

	{include file="inc/email_jobs.tpl" method="emailInfo" fixed=true}

</div>