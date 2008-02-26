<!-- start upload block-->
<script type="text/javascript">
<!--
{literal}
// close modal window, confirming options
function closeOK() {
	var tmp = new Array() ;
	$(":checkbox").each(function() {
		try {
			if(this.checked) { tmp[tmp.length] = $(this).attr("value") ;}
		} catch(e) {
		}
	}) ;
	try {
		{/literal}{$controller}{literal}CommitUploadById(tmp) ;
	} catch(e) {
		parent.{/literal}{$controller}{literal}CommitUploadById(tmp) ;
	}
}
function closeEsc() {
	try {
		{/literal}{$controller}{literal}RollbackUploadItem();
	} catch(e) {
		parent.{/literal}{$controller}{literal}RollbackUploadItem();
	}
}
function disableButtons() {
	$("#okqueuebtn").attr("disabled","disabled");
	$("#annullaqueuebtn").attr("disabled","disabled");
}
$(document).ready(function(){
	$(".selMultimedia").bind("click", function(){
		var check = $("input:checkbox",$(this).parent().parent()).get(0).checked ;
		$("input:checkbox",$(this).parent().parent()).get(0).checked = !check ;
	}) ;
});
//-->
{/literal}
</script>
</head>
<body>
<div>
<form action="{$html->url('/')}">
	<fieldset>
		{if !empty($items)}
		<p class="toolbar">
		{t}{$itemType}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('selectTop')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		</p>
		<table class="indexList">
		<tr>
			<th>&nbsp;</th>
			<th>{$beToolbar->order('id', 'id')}</th>
			<th>{$beToolbar->order('title', 'Title')}</th>
			<th>{$beToolbar->order('status', 'Status')}</th>
			<th>{$beToolbar->order('created', 'Created')}</th>
			<th>{t}Type{/t}</th>
			<th>{t}File name{/t}</th>
			<th>{t}MIME type{/t}</th>
			<th>{t}File size{/t}</th>
			<th>{$beToolbar->order('lang', 'Language')}</th>
		</tr>
		{section name="i" loop=$items}
		<tr class="rowList">
			<td><input type="checkbox" value="{$items[i].id}"/></td>
			<td><a class="selMultimedia" href="javascript:void(0);">{$items[i].id}</a></td>
			<td>{$items[i].title}</td>
			<td>{$items[i].status}</td>
			<td>{$items[i].created|date_format:'%b %e, %Y'}</td>
			<td>{$items[i].bedita_type}</td>
			<td>{$items[i].name}</td>
			<td>{$items[i].type}</td>
			<td>{$items[i].size}</td>
			<td>{$items[i].lang}</td>
		</tr>
		{/section}
		</table>
		<p class="toolbar">
		{t}{$itemType}{/t}: {$beToolbar->size()} | {t}page{/t} {$beToolbar->current()} {t}of{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp;
		{t}Dimensions{/t}: {$beToolbar->changeDimSelect('dimSelectBottom')} &nbsp;
		{t}Go to page{/t}: {$beToolbar->changePageSelect('pagSelectBottom')}
		</p>
		{else}
		{t}No {$itemType} found{/t}
		{/if}
	</fieldset>
</form>
<input type="button" id="okqueuebtn" onclick="javascript:closeOK();disableButtons();" value="{t}Ok{/t}"/>
<input type="button" id="annullaqueuebtn" onclick="javascript:closeEsc();disableButtons();" value="{t}Cancel{/t}"/>
</div>
<!-- end upload block -->