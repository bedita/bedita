{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")|escape}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div>
	
	<div class="menuleft insidecol">
	{if $method eq "templates"}

		<a class="bemaincommands" href="{$html->url('/newsletter/viewMailTemplate')}">{t}New template{/t}</a></li>


	{elseif $method eq "newsletters"}

		<a class="bemaincommands" href="{$html->url('/newsletter/viewMailMessage')}">{t}Create new{/t}</a>
			
	{elseif $method eq "mailgroups"}
	
		<a class="bemaincommands" href="{$html->url('/newsletter/viewMailGroup/')}">{t}Create new list{/t}</a>	
	
	{elseif $method eq "viewmailgroup"}

		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
	
	{elseif $method eq "invoices"}
	
	
	{elseif !empty($method) && $method != "index"}
	
		{if ($object.mail_status == "injob")}
			
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			
		{elseif ($object.mail_status == "sent")}
			
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
			
		{else}
			
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		
		{/if}
	
	{/if}
	</div>
	
</div>