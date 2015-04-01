<script type="text/javascript">
    $(document).ready(function(){
		openAtStart("#data-source, #data-options");

		$('.import-type input[type=radio]').click(function() {
			$('.import-file input[type=file]').prop( "disabled", false );

			var htmlOptions = '{t}Selected filter has no options{/t}.';
			var importOptions;
			// TODO
			if(importOptions) {
				options = '';
			}
			$('#data-options').html(htmlOptions);
		});
		/* drag&drop disabled, file upload should be managed in controller for preview
		var myDropzone = new Dropzone(".import .mainhalf", {
			url: "/home/importLoadFile"}
		);
		*/
    });
</script>

{$view->element('modulesmenu')}

{include file="inc/menuleft.tpl"}

{include file="inc/menucommands.tpl"}


<div class="head">
	<h1>{t}Import data{/t}</h1>
</div>

<div class="mainfull import">
<form action="{$html->url('/home/importData')}" method="post" name="importData" enctype="multipart/form-data">
{$beForm->csrf()}

	<div class="mainhalf">
		
		<div class="tab"><h2>{t}Source data{/t}</h2></div>

		<fieldset id="data-source">
			<div class="import-type">
				Import data type:
				<ul>
					{foreach $conf->filters.import as $filter => $val}
					<li>
						<input name="data[type]" type="radio" value="{$filter}" id="select-{$filter}" />
						<label for="select-{$filter}">{$filter}</label> &nbsp;
					</li>					
					{/foreach}
				</ul>
			</div>

			<hr>

			<div class="import-file">
				{t}Select file{/t}:
				<input type="file" name="file" disabled />
			</div>

			<div class="import-button-container">
				<input type="submit" value="load" />
			</div>
		</fieldset>

	</div>

{* SAMPLE RESPONSE			<!--
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
	
	<div class="mainhalf">
		<div class="tab"><h2>{t}Import options{/t}</h2></div>

		<fieldset id="data-options">
			<p>Seleziona un filtro di  importazione nei Dati sorgente.</p>
		</fieldset>

		{* SAMPLE OPTIONS <!--
			<select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
				<option>--</option>
				<option>Selezione della sezione dell'albero in cui importare</option>
				{$beTree->option($tree)}
			</select>
			<hr />
			<input type="checkbox" checked="true" /> include media
			<hr/>
			{t}Status{/t}: {html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
			
			<div id="finalimport" style="display:none; padding:10px 0px 10px 0px; margin:10px 0px 10px 0px; border-top:1px solid gray">
				<input type="submit" style="padding:10px" value="start import" />
			</div>
		--> *}
	</div>
</form>
</div>

