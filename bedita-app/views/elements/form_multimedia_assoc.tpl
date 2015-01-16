<!-- start upload block-->
<script type="text/javascript">
<!--

function addItemsToParent() { 
	var itemsIds = new Array() ;
	$(":checkbox").each(function() { 
		try { 
			if (this.checked && this.name == 'chk_bedita_item') { 
				itemsIds[itemsIds.length] = $(this).val();
			} 
		} catch(e) { 
		} 
	} ) ;
	for (i=0;i<itemsIds.length;i++) { 
		$("#tr_"+itemsIds[i]).remove();
	} 
	commitUploadItem(itemsIds, '{$relation}');
}

function loadMultimediaAssoc(urlSearch, showAll) { 
	$("#loading").show();
	$("#ajaxSubcontainer").load(urlSearch, function() { 
		$("#loading").hide();
		if (showAll) { 
			$("#searchMultimediaShowAll").show();
		} else { 
			$("#searchMultimediaShowAll").hide();
		} 
	} );
} 

$(document).ready(function(){ 

	$(".selItems").bind("click", function(){ 
		var check = $("input:checkbox",$(this).parent().parent()).get(0).checked ;
		$("input:checkbox",$(this).parent().parent()).get(0).checked = !check ;
	} );
	
	$("#searchMultimedia").bind("click", function() { 
		var textToSearch = escape($("#searchMultimediaText").val());
		loadMultimediaAssoc(
			"{$html->url('/streams/showStreams')}/{$object_id|default:'0'}/" + textToSearch,
			true
		);
	} );
	$("#searchMultimediaText").focus(function() { 
		$(this).val("");
	} );
	$("#searchMultimediaShowAll").click(function() { 
		loadMultimediaAssoc(
			"{$html->url("/streams/showStreams")}/{$object_id|default:'0'}",
			false
		);
	} );
	
	$("#addItems").click(function(){ 
		addItemsToParent();
	} );
	
	{if $toolbar|default:""}
	
		$("#streamPagList").tablesorter({ 
			headers: {  
				0: { sorter: false },
				2: { sorter: false } 
			} 
		} );
		 
		$("#streamNextPage").click(function() { 
			urlReq = "{$html->url("/streams/showStreams")}/{$object_id|default:'0'}/{$streamSearched|default:'0'}/{$toolbar.next}/{$toolbar.dim}";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamPrevPage").click(function() { 
			urlReq = "{$html->url("/streams/showStreams")}/{$object_id|default:'0'}/{$streamSearched|default:'0'}/{$toolbar.prev}/{$toolbar.dim}";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamFirstPage").click(function() { 
			urlReq = "{$html->url("/streams/showStreams")}/{$object_id|default:'0'}/{$streamSearched|default:'0'}/{$toolbar.first}/{$toolbar.dim}";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamLastPage").click(function() { 
			urlReq = "{$html->url("/streams/showStreams")}/{$object_id|default:'0'}/{$streamSearched|default:'0'}/{$toolbar.last}/{$toolbar.dim}";
			loadMultimediaAssoc(urlReq,	false);
		} );
		$("#streamPagDim").change(function() { 
			urlReq = "{$html->url("/streams/showStreams")}/{$object_id|default:'0'}/{$streamSearched|default:'0'}/{$toolbar.first}/" + $(this).val();
			loadMultimediaAssoc(urlReq,	false);
		} );
	
	{/if}
	
} );
//-->

</script>

<div id="formMultimediaAssoc" class="ignore">
	<fieldset>
		{if !empty($items)}
			{if $toolbar|default:""}
				<p>		
					{t}Items{/t}: {$toolbar.size} | {t}page{/t} {$toolbar.page} {t}of{/t} {$toolbar.pages} 

						&nbsp; | &nbsp;
						{if $toolbar.page > 1}
							<span><a href="javascript: void(0);" id="streamFirstPage" title="{t}first page{/t}">{t}first{/t}</a></span>
						{else}
							<span>{t}first{/t}</span>
						{/if}
						
						&nbsp; | &nbsp;
						
						{if $toolbar.prev > 0}
							<span><a href="javascript: void(0);" id="streamPrevPage" title="{t}previous page{/t}">{t}prev{/t}</a></span>
						{else}
							<span>{t}prev{/t}</span>
						{/if}
			
						&nbsp; | &nbsp;			
					
						{if $toolbar.next > 0}
							<span><a href="javascript: void(0);" id="streamNextPage" title="{t}next page{/t}">{t}next{/t}</a></span>
						{else}
							<span>{t}next{/t}</span>
						{/if}
						
						&nbsp; | &nbsp;
						
						{if $toolbar.last > 0}
							<span><a href="javascript: void(0);" id="streamLastPage" title="{t}last page{/t}">{t}last{/t}</a></span>
						{else}
							<span>{t}last{/t}
						{/if}
										
						&nbsp; | &nbsp;
					
						{t}Dimensions{/t}: 
						<select name="streamPagDim" id="streamPagDim">
							<option value="1"{if $toolbar.dim == 1} selected="selected"{/if}>1</option>
							<option value="5"{if $toolbar.dim == 5} selected="selected"{/if}>5</option>
							<option value="10"{if $toolbar.dim == 10} selected="selected"{/if}>10</option>
							<option value="20"{if $toolbar.dim == 20} selected="selected"{/if}>20</option>
							<option value="50"{if $toolbar.dim == 50} selected="selected"{/if}>50</option>
							<option value="100"{if $toolbar.dim == 100} selected="selected"{/if}>100</option>
						</select>
				</p>
			<hr />
			{/if}
		{/if}
		<div>
			<input type="text" id="searchMultimediaText" name="searchMultimediaItems" value="{if !empty($streamSearched)}{$streamSearched}{else}search{/if}"/>
			<input id="searchMultimedia" type="button" value="{t}Search{/t}"/>
			<input type="button" id="searchMultimediaShowAll" value="{t}Show all{/t}" style="display: none;" />
		</div>

		<hr />
		<table class="indexlist" id="streamPagList" style="clear: left;">
			{* <thead>
			<tr>
				<th></th>
				<th></th>
				<th>id</th>
				<th>{t}title{/t}</th>
				<th>{t}file{/t}</th>
				<th>{t}size{/t}</th>
				<th>{t}lang{/t}</th>
			</tr>
			</thead>
 			*}
			<tbody>
			
			{assign var="thumbWidth" value = 45}
			{assign var="thumbHeight" value = 45}
			{assign_associative var="params" presentation="thumb" width=$thumbWidth height=$thumbHeight}
			{assign_associative var="attributes" style="width:45px;"}
			
			{foreach from=$items item='mobj' key='mkey'}	
			<tr class="rowList" id="tr_{$mobj.id}">
				
				<td style="width:12px;"><input type="checkbox" value="{$mobj.id}" name="chk_bedita_item" class="objectCheck"/></td>
				
				<td style="width:{$thumbWidth}px;">
				<a title="show details" href="{$html->url('/multimedia/view/')}{$mobj.id}" target="_blank">
					{$beEmbedMedia->object($mobj,$params, $attributes)}
				</a>
				</td>
				
				{* <td>{$mobj.id}</td> *}
				<td>{$mobj.title|escape|default:""}</td>
				{* <td>{$mobj.name|default:""|truncate:24:"..."}</td> *}
				<td>{$mobj.file_size|default:""|filesize}</td>
				
				<td>{$mobj.lang}</td>
				
			</tr>
			{foreachelse}
				<tr>
					<td>{t}No {$itemType} item found{/t}</td>
				</tr>
			{/foreach}
			</tbody>
			</table>
		{if !empty($items)}
			<hr />
			&nbsp;<input type="checkbox" class="selectAll" id="selectAll" />&nbsp;
			<label for="selectAll"> {t}(Un)Select All{/t}</label>
			&nbsp;&nbsp;&nbsp;
			<input type="button" id="addItems" value="{t}Add selected items{/t}"/>
		{/if}
	</fieldset>
	
</div>
<!-- end upload block -->