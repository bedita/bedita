<form action="{$html->url('/search')}" method="post">
	<div><label class="screen-reader-text" for="s">Search for:</label>
	<input type="text" name="searchstring" value="{$stringSearched|default:''}" id="s"/>
	<input type="submit" value="{t}search{/t}"/>
</form>
