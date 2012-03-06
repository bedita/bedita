{if $what == "newsletter" && !empty($mailgroups)}
	{$view->element('subscribe_newsletter')}
{elseif $what == "user"}
	{$view->element('signup')}
{/if}	