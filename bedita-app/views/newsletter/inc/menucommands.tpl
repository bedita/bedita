{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<script type="text/javascript">
{literal}
$(document).ready(function(){
	$("#delBEObject").submitConfirm({
		{/literal}
		action: "{if !empty($delparam)}{$html->url($delparam)}{else}{$html->url('delete/')}{/if}",
		message: "{t}Are you sure that you want to delete the item?{/t}",
		formId: "updateForm"
		{literal}
	});
	
	$("#delAddress").submitConfirm({
		{/literal}
		action: "{$html->url('deleteAddress/')}",
		message: "{t}Are you sure that you want to delete the address?{/t}",
		formId: "updateForm"
		{literal}
	});
	
	$("div.insidecol input[@name='save']").click(function() {
		$("#updateForm").submit();
	});

});
</script>
{/literal}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	<div class="modules">
	   <label class="{$moduleName}" rel="{$html->url('/newsletter')}">{t}{$currentModule.label}{/t}</label>
	</div> 
	

	{assign var="user" value=$session->read('BEAuthUser')}


	{if $method eq "templates"}

		<ul class="menuleft insidecol bordered">
			<li><a href="{$html->url('/newsletter/viewtemplate')}">{t}New template{/t}</a></li>
		</ul>

	{elseif $method eq "newsletters"}
	
		{literal}
		<style>
			UL#templates {
				margin-left:0px; 
				margin-top:10px;
				display:none;
				
			}
			UL#templates LI {
				list-style-type:none; padding-left:0px;
				cursor:pointer;	
			}
			UL#templates LI:Hover {
				font-weight:bold;
			}
			
		</style>
		{/literal}
		
		<ul class="menuleft insidecol">
			<li {if $method eq "view"}class="on"{/if}><a href="{$html->url('/newsletter/view')}">{t}Create new{/t}</a></li>
		</ul>
			
		<ul class="menuleft insidecol">
			<li><a href="javascript:void(0)" onClick="$('#templates').slideToggle();">{t}Select by template{/t}</a></li>
				<ul id="templates" class="bordered">
					<li>pubblicazione uno</li>
					<li>pubblic azione 2</li>
					<li>pu blic azione III</li>
					<li>Quarta pubblicazione</li>
					<li class="on">All</li>
				</ul>
		</ul>
	
	{elseif $method eq "mailgroups"}
	
		<ul class="menuleft insidecol">
			<li><a href="{$html->url('/newsletter/view_mail_group/')}">{t}Create list{/t}</a></li>
		</ul>
	
	{elseif $method eq "viewmailgroup"}

		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />
		</div>

	{elseif $method eq "invoices"}
	
	
	{elseif !empty($method) && $method != "index" && $module_modify eq '1'}
	
		<div class="insidecol">
			{if ($perms->isWritable($user.userid,$user.groups,$object.Permissions))}
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" />
			{/if}
			{if ($perms->isDeletable($user.userid,$user.groups,$object.Permissions))}
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" {if !($object.id|default:false)}disabled="1"{/if} />
			{/if}
		</div>
	
	{/if}


	

</div>

