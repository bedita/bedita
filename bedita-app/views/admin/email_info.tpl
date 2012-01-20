{$html->script("jquery/jquery.treeview", false)}
{$html->script("form", false)}

<script type="text/javascript">
	var message = "{t}Are you sure that you want to delete the item?{/t}";
	var delLogUrl = '{$html->url('/admin/deleteMailLog')}';

	$(document).ready(function() { 
		$("#email_jobs").prev(".tab").BEtabstoggle();
		$("#email_summary").prev(".tab").BEtabstoggle();
		$(".delLog").bind("click", function() { 
			if(!confirm(message))
				return false ;
			var logId = $(this).attr("title");
			$("#form_log_"+logId).attr("action", delLogUrl + '/' + logId).submit();
			return false;
		} );

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