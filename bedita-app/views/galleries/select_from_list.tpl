<script type="text/javascript">
{literal}
// funzione per la chiusura della finestra modale confermando le operazioni
function closeOKBEdita() {
	var tmp = 0 ;
	var title = "" ;
	
	$(":radio").each(function() {
		try {
			if(this.checked) {
				tmp = $(this).attr("value") ;
				title = $("input:hidden",$(this).parent()).get(0).value ;
			}
		} catch(e) {
			
		}
		
	}) ;
	
	try {
		commitSelectGalleryById(tmp, title) ;
	} catch(e) {
		parent.commitSelectGalleryById(tmp, title) ;
	}
}

// funzione per la chiusura della finestra modale annullando le operazioni
var counter = 0 ;
function closeEsc() {
	try {
		rollbackSelectGallery() ;
	} catch(e) {
		parent.rollbackSelectGallery() ;
	}
}

$(document).ready(function(){
	$(".selGallery").bind("click", function(){
		var check = $("input:radio",$(this).parent().parent()).get(0).checked ;
		$("input:radio",$(this).parent().parent()).get(0).checked = !check ;
	}) ;

});

{/literal}
</script>	
<div>
	<form>
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
		<th></th>
		<th>{$beToolbar->order('id', 'id')}</th>
		<th>{$beToolbar->order('title', 'Title')}</th>
		<th>{$beToolbar->order('status', 'Status')}</th>
		<th>{$beToolbar->order('created', 'Created')}</th>
		<th>{$beToolbar->order('lang', 'Language')}</th>
	</tr>
	</thead>
	<tbody>
	{section name="i" loop=$galleries}
	<tr class="rowList">
		<td>
		<input type="radio" name="gallery" value="{$galleries[i].id}"/>
		<input type="hidden" name="title" value="{$galleries[i].title|escape:'quote'}"/>
		</td>
		<td><a class="selGallery" href="javascript:void(0);">{$galleries[i].id}</a></td>
		<td>{$galleries[i].title}</td>
		<td>{$galleries[i].status}</td>
		<td>{$galleries[i].created|date_format:$conf->date_format}</td>
		<td>{$galleries[i].lang}</td>
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
	<a class="swfuploadbtn" id="okqueuebtn" href="javascript:closeOKBEdita();" style="display:block">Ok</a>
	<a class="swfuploadbtn" id="annullaqueuebtn" href="javascript:closeEsc();" style="display:block">Annulla</a>					  	
  	</form>
</div>