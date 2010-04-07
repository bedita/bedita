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
	<p>{$content_for_layout}</p>
</div>
{/strip}