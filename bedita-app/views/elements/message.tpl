{strip}

{* strip required for javascript use, see views/errors/error_ajax.tpl (output == "beditaMsg") *}

<div class="message {$class}">
	{if $class == "info"}
		<h2>{t}Notice{/t}</h2>
	{elseif $class == "warn"}
		<h2>{t}Warning{/t}</h2>
	{elseif $class == "error"}
		<h2>{t}Error{/t}</h2>
	{/if}
	<p style="display:block; margin-top:10px; min-height: 30px; overflow: visible; word-wrap: break-word;">
		{$message|escape}
	</p>
	<hr />
	{if !empty($detail)}
		<a href="javascript:void(0)" onclick="$('.messageDetail').toggle()">
			{t}see error detail{/t}</a>&nbsp;&nbsp;&nbsp;&nbsp;
	{/if}
		<a class="closemessage" href="javascript:void(0)">
			{t}close{/t}</a>	
</div>
	
{if !empty($detail)}
<div class="messageDetail shadow" style="display:none">	
	<p style="font-family:monospace;">{$detail|escape}</p>
	<hr />
	<a class="closemessage" href="javascript:void(0)">
		{t}close{/t}</a>	
</div>
{/if}
	

{/strip}