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