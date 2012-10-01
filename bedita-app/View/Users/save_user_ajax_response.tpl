{*
*	return a JSON object for ajax response
*	if userCreated setted put JSON object into textarea (trick for user created)
*}
{strip}
{if $errorMsg|default:""}
{SaveErrorMsg: "{$errorMsg}"}
{else}
{userId:"{$userId|default:''}"}
{/if}
{/strip}