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
	<div class="closemessage"><a href="javascript: void(0);">{t}close{/t}</a></div>
	<p>{$message}</p>
	{if !empty($detail)}
	<div>
		<a href="javascript: void(0);" class="messagedetail">{t}see error detail{/t}</a>
		<p style="display: none;">{$detail}</p>
	</div>
	{/if}
</div>
{/strip}