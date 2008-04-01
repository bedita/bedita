{*
*	return a JSON object for ajax response
*
*}

{strip}
<textarea>
{if $errorMsg|default:""}
	{literal}{{/literal}UploadErrorMsg: "{$errorMsg}"{literal}}{/literal}
{else}
	[
		{literal}{{/literal}
			filename:	"{$fileName}"
		{literal}}{/literal}
	]
{/if}
</textarea>
{/strip}