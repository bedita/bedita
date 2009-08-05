<h2>Error 500 - Internal Server Error </h2>

{if $conf->debug >= 1} 
<pre>
ErrorType:	{$errorType|default:''}
Details: 	{$details|default:''}
Result: 	{$result|default:''}

Action: 	{$action|default:''}
Controller: {$controller|default:''}
File: 		{$file|default:''}
Title: 		{$title|default:''}

</pre>
{/if}