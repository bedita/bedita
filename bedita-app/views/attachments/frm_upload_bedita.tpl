<!-- inizio blocco upload -->
<div>
<script type="text/javascript">
<!--
{literal}

var files = {} ;	// file in coda


// funzione per la chiusura della finestra modale confermando le operazioni
function closeOKBEdita() {
	var tmp = new Array() ;
	
	$(":checkbox").each(function() {
		try {
			if(this.checked) {
				tmp[tmp.length] = $(this).attr("value") ;
			}
		} catch(e) {
			
		}
		
	}) ;
	
	try {
		commitUploadAttachById(tmp) ;
	} catch(e) {
		parent.commitUploadAttachById(tmp) ;
	}
}

// funzione per la chiusura della finestra modale annullando le operazioni le operazioni
var counter = 0 ;
function closeEsc() {
	try {
		rollbackUploadAttachment() ;
	} catch(e) {
		parent.rollbackUploadAttachment() ;
	}
}


$(document).ready(function(){
	$(".selAttach").bind("click", function(){
		var check = $("input:checkbox",$(this).parent().parent()).get(0).checked ;
		$("input:checkbox",$(this).parent().parent()).get(0).checked = !check ;
	}) ;

});

//-->
{/literal}
</script>

<style type="text/css">
{literal}
{/literal}
</style>

</head>
<body>
<div>
	<form>
		<p class="toolbar">
		{t}Attachments{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp; 
		{t}Dimensioni{/t}: {$beToolbar->changeDim()} &nbsp;
		{t}Vai alla pagina{/t}: {$beToolbar->changePage()}
		</p>
			<table class="indexList">
				<tr>
					<th></th>
					<th>{$beToolbar->order('id', 'id')}</th>
					<th>{$beToolbar->order('title', 'titolo')}</th>
					<th>{$beToolbar->order('status', 'status')}</th>
					<th>{$beToolbar->order('created', 'creato il')}</th>
					<th>type</th>
					<th>file name</th>
					<th>MIME type</th>
					<th>file size</th>
					<th>{$beToolbar->order('lang', 'lingua')}</th>
				</tr>
				{section name="i" loop=$attachments|default:false}
					<tr class="rowList">
						<td><input type="checkbox" value="{$attachments[i].id}"/></td>
						<td><a class="selAttach" href="javascript:void(0);">{$attachments[i].id}</a></td>
						<td>{$attachments[i].title}</td>
						<td>{$attachments[i].status}</td>
						<td>{$attachments[i].created|date_format:'%b %e, %Y'}</td>
						<td>{$attachments[i].bedita_type}</td>
						<td>{$attachments[i].name}</td>
						<td>{$attachments[i].type}</td>
						<td>{$attachments[i].size}</td>
						<td>{$attachments[i].lang}</td>
					</tr>				
				{/section}
			</table>
		<p class="toolbar">
		{t}Attachments{/t}: {$beToolbar->size()} | {t}pagina{/t} {$beToolbar->current()} {t}di{/t} {$beToolbar->pages()} &nbsp;
		{$beToolbar->first()} &nbsp; {$beToolbar->prev()}  &nbsp; {$beToolbar->next()} &nbsp; {$beToolbar->last()} &nbsp; 
		{t}Dimensioni{/t}: {$beToolbar->changeDim()} &nbsp;
		{t}Vai alla pagina{/t}: {$beToolbar->changePage()}
		</p>
	</form>
	<a class="swfuploadbtn" id="okqueuebtn" href="javascript:closeOKBEdita();" style="display:block">Ok</a>
	<a class="swfuploadbtn" id="annullaqueuebtn" href="javascript:closeEsc();" style="display:block">Annulla</a>					
	
</div>
<!-- fine blocco upload -->