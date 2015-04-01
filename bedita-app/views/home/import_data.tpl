<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#import-result");
    });
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Import data{/t}</h1>
</div>

<div class="mainfull import">

	{if !empty($result.error)}
		<div class="import-message import-error">
			<h2>{t}Import error{/t}</h2>
			<div>{$result.error}</div>
		</div>
	{/if}

	<div class="tab stayopen"><h2>{t}Results{/t}</h2></div>

	<fieldset id="import-result">
		<div>
			<p>{t}Number of imported objects:{/t} {$result.objects|default:'0'}</p>
		</div>

		<div class="import-message">
			{$result.message|default:''}
		</div>
	</fieldset>

{* <!-- EXAMPLE REPORT 
		<div id="import" style="display:none">
		<label>Objects ready to import:</label>
		<ul>
			<li>12 sections</li>
			<li>46 dopcuments</li>
			<li>18 media</li>
		</ul>
		<hr />
		<label>Error log:</label>
		<ul>
			<li>12 media path not found</li>
			<li>Qui ilò full log</li>
		</ul>
		</div>
--> *}

</div>

