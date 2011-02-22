{*
*	return a JSON object for ajax response
*	if userCreated setted put JSON object into textarea (trick for user created)
*}
{strip}
{if $errorMsg|default:""}
{literal}{{/literal}SaveErrorMsg: "{$errorMsg}"{literal}}{/literal}
{else}
{literal}{{/literal}userId:"{$userId|default:''}"{literal}}{/literal}
{/if}
{/strip}