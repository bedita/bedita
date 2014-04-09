{*
*	return a JSON object for ajax response
*	if fileUploaded is set put JSON object into textarea (trick for file upload)
*}
{strip}

{if $errorMsg|default:""}
	{ "UploadErrorMsg": "{$errorMsg}" }
{else}
	[
		{
			"fileId":	"{$fileId|default:''}"
		}
	]
{/if}

{/strip}