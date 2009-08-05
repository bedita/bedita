<form action="{$html->url('/search')}" method="post">
	<input type="text" name="searchstring" value="{$stringSearched|default:''}"/>
	<input type="submit" value="{t}search{/t}"/>
</form>