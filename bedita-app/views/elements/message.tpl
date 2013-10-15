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
	<p style="display:block; margin-top:10px; max-height: 30px; overflow: hidden; word-wrap: break-word;">
		{$message}
	</p>
	<hr />
	{if !empty($detail)}
		<a class="close" href="javascript:void(0)" onclick="$('.messageDetail').toggle()">
			{t}see error detail{/t}</a>
	{/if}
		<a class="close" href="javascript:void(0)" onClick="$('#messagesDiv').fadeOut()">
			{t}close{/t}</a>	
</div>
	
{if !empty($detail)}
<div class="messageDetail shadow" style="display:none">	
	<p style="font-family:monospace;">{$detail}</p>
	<hr />
	<a class="close" href="javascript:void(0)" onClick="$('.messageDetail').fadeOut()">
		{t}close{/t}</a>	
</div>
{/if}
	

{/strip}