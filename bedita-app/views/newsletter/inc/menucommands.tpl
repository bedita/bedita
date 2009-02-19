{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}


<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($method) && $method != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 0=$html->url('/') 1=$currentModule.path}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div>
	
	
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
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		</div>

	{elseif $method eq "invoices"}
	
	
	{elseif !empty($method) && $method != "index"}
	
		<div class="insidecol">
			<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="save" id="saveBEObject" />
			<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
			<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
		</div>
	
	{/if}


	

</div>

