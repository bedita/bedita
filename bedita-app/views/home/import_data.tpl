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

	<div class="tab"><h2>{t}Results{/t}</h2></div>

	<fieldset id="import-result">
		
	{dump var=$result}

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

