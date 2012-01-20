{$html->script("jquery/jquery.treeview", false)}
{$html->script("form", false)}

<script type="text/javascript">
	$(document).ready(function() { 
		$("#email_jobs").prev(".tab").BEtabstoggle();
		$("#email_summary").prev(".tab").BEtabstoggle();
		$("#single_emails").prev(".tab").BEtabstoggle();
	} );
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl" method="emailInfo"}

{include file="inc/menucommands.tpl" method="emailInfo" fixed=true}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
		<h2>{t}Mail Jobs{/t}</h2>
		{include file="./inc/toolbar.tpl" label_items='jobs'}
	</div>
</div>

<div class="mainfull">
	{include file="inc/email_jobs.tpl" method="emailInfo" fixed=true}
</div>