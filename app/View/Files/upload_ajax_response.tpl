{*
*	return a JSON object for ajax response
*	if fileUploaded setted put JSON object into textarea (trick for file upload)
*}

{strip}

{if !empty($fileUploaded)}
<textarea>
{/if}

{if $errorMsg|default:""}
	{ "UploadErrorMsg": "{$errorMsg}" }
{else}
	[
		{
			"fileId":	"{$fileId|default:''}"
		}
	]
{/if}

{if !empty($fileUploaded)}
</textarea>
{/if}

{/strip}