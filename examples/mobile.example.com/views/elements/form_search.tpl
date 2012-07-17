

<form action="{$html->url('/search')}" method="post">
	<input type="search" name="searchstring" id="searc-basic" "{$stringSearched|default:''}" placeholder="{t}what are you looking for?{/t}"/>
	<input type="submit" value="{t}search{/t}" />
</form>