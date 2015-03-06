{$html->script("libs/jquery/plugins/jquery.form", false)}

<script type="text/javascript">
    $(document).ready(function() {
		openAtStart("#details");
    });

	var urlBan = "{$html->url('/comments/banIp')}";
	var msgBan = "{t}Are you sure you want to ban this IP?{/t}";
	var msgAccept = "{t}Are you sure you want to accept this IP?{/t}";

	$(document).ready(function() {
		$("#banIP").bind("click", function() {
			if (!confirm(msgBan)) {
				return false;
			}
			$("#updateForm").prop("action", urlBan).submit();
			return false;
		});
		$("#sbanIP").bind("click", function() {
			if (!confirm(msgAccept)) {
				return false;
			}
			$("#updateForm").prop("action", urlBan).submit();
			return false;
		});
	});

</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

<div class="head">
	
	<h1>{if !empty($object)}{$object.title|escape|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>

</div>

{assign var=objIndex value=0}

{include file="inc/menucommands.tpl" fixed=true}

<div class="main">	
	
	{include file="inc/form.tpl"}
		
</div>

{$view->element('menuright')}



