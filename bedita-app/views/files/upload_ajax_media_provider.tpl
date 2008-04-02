{*
*	return a JSON object for ajax response
*
*}

{strip}

{if $errorMsg|default:""}
	{literal}{{/literal}UploadErrorMsg: "{$errorMsg}"{literal}}{/literal}
{else}

		{literal}{{/literal}
			filename:	"{$filename|default:''}"
		{literal}}{/literal}

{/if}

{/strip}
{php}exit;{/php}