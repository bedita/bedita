{if $conf->uploadType == "ajax"}
	{$javascript->link("jquery.form")}
	{include file="../pages/form_upload_ajax.tpl" controller="attachments"}
{else if $conf->uploadType == "flash"}
	{include file="../pages/form_upload.tpl" controller="attachments"}
{/if}