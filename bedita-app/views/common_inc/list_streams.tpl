<script type="text/javascript">
var URLBase = "{$html->url('index/')}" ;
var urlDelete = "{$html->url('delete/')}";
var message = "{t}Are you sure that you want to delete the item?{/t}";
<!--
{literal}
$(document).ready(function(){
	//$("#tree").designTree({url:URLBase}) ;
	
	$("TABLE.indexList TD.cellList").click(function(i) { 
		document.location = $(this).parent().find("a:first").attr("href"); 
	} );
	
	$("a.delete").bind("click", function() {
		if(!confirm(message)) return false ;
		var idToDel = $(this).attr("title");
		$("#multimToDel").attr("value",idToDel);
		$("#formObject").attr("action", urlDelete) ;
		$("#formObject").get(0).submit() ;
	});
});
{/literal}
//-->
</script>	


		
{if !empty($objects)}
	<form method="post" action="" id="formObject">

	<input type="hidden" id="multimToDel" name="data[id]"/>

	</form>
{/if}
	
<table class="indexlist">

	<tr>
		
		<th style="width:155px" nowrap>
			{*t}Thumb{/t*} 
			<img class="multimediaitemToolbar" src="/img/px.gif" />
			order by:</th>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{t}Name{/t}</th>
		<th>{t}Type{/t}</th>
		<th>{t}Size{/t}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
	</tr>
	
	{section name="i" loop=$objects}
	<tr class="rowList" rel="{$html->url('view/')}{$objects[i].id}">
		<td>
			{assign var="thumbWidth" 		value = 90}
			{assign var="thumbHeight" 		value = 60}
			{assign var="filePath"			value = $objects[i].path}
			{assign var="mediaPath"         value = $conf->mediaRoot}
			{assign var="mediaUrl"         value = $conf->mediaUrl}
			{assign_concat var="mediaCacheBaseURL"	0=$conf->mediaUrl  1="/" 2=$conf->imgCache 3="/"}
			{assign_concat var="mediaCachePATH"		0=$conf->mediaRoot 1=$conf->DS 2=$conf->imgCache 3=$conf->DS}

				
		<div class="multimediaitem">
		
		{if strtolower($objects[i].ObjectType.name) == "image"}	
		
		{thumb 
			longside 		= 90
			width			= $thumbWidth
			height			= $thumbHeight
			sharpen			= "false"
			file			= $mediaPath$filePath
			link			= "false"
			linkurl			= $mediaUrl$filePath
			window 			= "false"
			cache			= $mediaCacheBaseURL
			cachePATH		= $mediaCachePATH
			hint			= "false"
			html			= ""
			frame			= ""
		}	
			
				
	{elseif ($objects[i].provider|default:false)}
	
				{assign_associative var="attributes" style="width:30px;heigth:30px;"}
				<a href="{$filePath}" target="_blank">{$mediaProvider->thumbnail($objects[i], $attributes) }</a>
	
	{else}
				<a href="{$conf->mediaUrl}{$filePath}" target="_blank"><img src="{$session->webroot}img/mime/{$objects[i].type}.gif" /></a>
	{/if}
		</div>
			
		</td>
		<td>{$objects[i].id}</td>
		<td>{$objects[i].title}</td>
		<td>{$objects[i].name}</td>
		<td>{$objects[i].ObjectType.name}</td>
		<td>{math equation="x/y" x=$objects[i].size|default:0 y=1024 format="%d"|default:""} KB</td>
		<td>{$objects[i].status}</td>
		<td>{$objects[i].created|date_format:'%b %e, %Y'}</td>
	</tr>				
		{sectionelse}
		
			<td colspan="100" style="padding:30px">{t}No {$moduleName} found{/t}</td>
		
		{/section}
	</table>


<fieldset style="padding-bottom:15px;" id="multimediaitems">

<div style="margin-top:10px;">
{section name=e loop=4}
<div class="multimediaitem">
	<img src="img/thumb2.jpg" />
	<ul>
		<li>titolo dell'immagine</li>
		<li>thumb2.jpg</li>
		<li>80 Kb</li>
	</ul>
</div>

<div class="multimediaitem">
	<img src="img/thumb.jpg" />
	<ul>
		<li>io sono il titolo dell'immagine</li>
		<li>thumb.jpg</li>
		<li>780 Kb</li>
	</ul>
</div>
{/section}
</div>

</fieldset>

<div class="tab"><h2>Operazioni sui 3 records selezionati</h2></div>
<div>
	<input type="checkbox" class="selectAll" id="selectAll"/><label for="selectAll"> {t}(Un)Select All{/t}</label>
	<hr />
	<input id="deleteSelected" type="button" value="X {t}Delete selected items{/t}"/>
</div>




