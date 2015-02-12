<script type="text/javascript">
$(document).ready(function() {
	$(".mainfull .modules *").unbind("click");

	$("#plugged").sortable({
		distance: 20,
		opacity: 0.7,
		update: function() {
			$("#plugged li input[type=hidden]").each(function(index) {
				//alert(index);
				$(this).val(parseInt(index)+parseInt(1));
			});
		}
	}).css("cursor","move");;
});
</script>

<style>

	.modules#plugged {
		margin-bottom:20px;
	}


	.modules.block LI {
		margin-right:10px;
		margin-bottom:10px;
		float:left;
	}

</style>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl" fixed=true}

<div class="mainfull">

{if !empty($moduleList)}

	<div class="tab stayopen"><h2>{t}All modules{/t}</h2></div>

	<form action="{$html->url('/admin/sortModules')}" id="sortModules" method="post">
	{$beForm->csrf()}

	<ul class="modules block" id="plugged">
	{foreach from=$moduleList key=k item=mod}
		<li class="{$mod.name}">
			{t}{$mod.label}{/t}
			<input type="hidden" name="data[Modules][{$mod.id}]" value="{$mod.priority}"/>
		</li>
	{/foreach}
	</ul>

	</form>
{/if}


</div>