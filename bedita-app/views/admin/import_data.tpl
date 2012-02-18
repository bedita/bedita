{literal}
<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#source,#destination");
    });
</script>
{/literal}

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Import data{/t}</h1>
</div>



<div class="mainfull">

	<div class="mainhalf">
		
	<div class="tab"><h2>{t}Source data{/t}</h2></div>
		<fieldset id="source">
			
			<div id="preload">
			
			{t}Select file(s){/t}: <input type="file" />
			<hr />
			Import data type: &nbsp;
			<input type="radio" name="import_data_type">BEdita &nbsp;
			<input type="radio" name="import_data_type">epub3 &nbsp;
			<input type="radio" name="import_data_type">Zxml
			<div style="text-align:center; padding:10px; margin:10px 0px 10px 0px; border-top:1px solid gray">
			<input type="button" value="load" onclick="$('#import').show(); $('#preload').hide(); $('#finalimport').show()" />
			</div>
			</div>
			
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
			
		</fieldset>
	</div>
	
	<div class="mainhalf">
	<div class="tab"><h2>{t}Destination{/t}</h2></div>
		<fieldset id="destination">
		<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
			<option>--</option>
			<option>Selezione della sezione dell'albero in cui importare</option>
			{*$beTree->option($tree)*}
		</select>
		<hr />
		<input type="checkbox" checked="true" /> include media
		<hr/>
		{t}Status{/t}: {html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
		
<div id="finalimport" style="display:none; padding:10px 0px 10px 0px; margin:10px 0px 10px 0px; border-top:1px solid gray">
					
					<input type="submit" style="padding:10px" value="start import" onclick="$('#import').show()" />
				</div>
				

		</fieldset>
	</div>
	
</div>
