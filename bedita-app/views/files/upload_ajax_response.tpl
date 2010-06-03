{*
*	return a JSON object for ajax response
*	if fileUploaded setted put JSON object into textarea (trick for file upload)
*}

{strip}

{if !empty($fileUploaded)}
<textarea>
{/if}

{if $errorMsg|default:""}
	{literal}{{/literal}"UploadErrorMsg": "{$errorMsg}"{literal}}{/literal}
{else}
	[
		{literal}{{/literal}
			"fileId":	"{$fileId|default:''}"
		{literal}}{/literal}
	]
{/if}

{if !empty($fileUploaded)}
</textarea>
{/if}

{/strip}
{php}exit;{/php}