{if !empty($BEAuthUser.userid)}
<ul class="bordered">
	<li>{t}User{/t}: {$BEAuthUser.userid|upper}</li>
	{if isset($module_modify)}<li>{t}Permission{/t}: {if $module_modify eq '1'}{t}Modify{/t}{else}{t}Read{/t}{/if}</li>{/if}
</ul>
{/if}
