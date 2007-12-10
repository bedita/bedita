<script type="text/javascript">
<!--
var urlDelete 	= "{$html->url('delete/')}" ;
var message	= "{t}Are you sure that you want to delete the gallery?{/t}" ;

{literal}

$(document).ready(function(){
	$("TABLE.indexList TR.rowList").click(function(i) {
		document.location = $("A", this).attr('href') ;
	} );
	
	// conferma per la cancellazione di un oggetto direttamente dalal lista
	$(".delete").bind("click", function(e) {
		if(!confirm(message)) return false ;
		$("#frmDelete //input[@name='data[id]']").attr("value", $(this).attr("id").substring(1)) ;
		$("#frmDelete").attr("action", urlDelete) ;
		$("#frmDelete").get(0).submit() ;
		
		return false ;
	}) ;
		
});

function localConfirm(anchorElem,url) {
	var msg = "{/literal}{t}Are you sure that you want to delete the gallery?{/t}{literal}";
	var confirmed = confirm(msg);
	anchorElem.href = (confirmed) ? url : "#";
}
{/literal}
//-->
</script>

<div id="containerPage">
	
	<div id="listGalleries">
	<form method="post" action="" id="frmDelete"><input type="hidden" name="data[id]"/></form>
	{if $galleries}
	<p class="toolbar">
		{t}Galleries{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
	<table class="indexList" cellpadding="0" cellspacing="0" style="width:578px">
	<thead>
	<tr>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
		<th>-</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$galleries}
	<tr class="rowList">
		<td><a href="{$html->url('view/')}{$galleries[i].id}">{$galleries[i].id}</a></td>
		<td>{$galleries[i].title}</td>
		<td>{$galleries[i].status}</td>
		<td>{$galleries[i].created|date_format:$conf->date_format}</td>
		<td>{$galleries[i].lang}</td>
		<td><a href="javascript:void(0);" class="delete" id="g{$galleries[i].id}">{t}Delete{/t}</a></td>
	</tr>
	{/section}
	</tbody>
	</table>
	<p class="toolbar">
		{t}Galleries{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
	</p>
  	{else}
  	{t}No galleries found{/t}
  	{/if}
	</div>

</div>