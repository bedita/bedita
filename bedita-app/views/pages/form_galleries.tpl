<h2 class="showHideBlockButton">{t}Connect to multimedia gallery{/t}</h2>
<div class="blockForm" id="frmgallery" style="display:none">

{if !empty($object.gallery_id)}
<script type="text/javascript">
var sBasePathGallery  = "{$html->url('/galleries/view/id:')}" ;
{literal}
function commitSelectGalleryById(id, title) {
	if(id) {
		$("#gallery_id").attr("value", id) ;
		$("#current_gallery_title").html(title) ;
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
	}		
	tb_remove() ;	
}

function rollbackSelectGallery() {
	tb_remove() ;
}

$(document).ready(function(){
	$("#remove_gallery").bind("click", function(index) {
		$("#gallery_id").attr("value", "") ;
		$("#current_gallery_title").html("") ;
		
		// Indica l'avvenuto cambiamento dei dati
		try { $().alertSignal() ; } catch(e) {}
	}) ;
});

{/literal}
</script>
<fieldset>
<input type="hidden" id="gallery_id" name="data[gallery_id]" value="{$object.gallery_id}" />
<p>
<a href="{$html->url('/galleries')}/select_from_list/?keepThis=true&TB_iframe=true&height=480&width=640&modal=true" title="{t}Select{/t}" class="thickbox">{t}Select{/t} &gt;&gt;</a>&nbsp;&nbsp; 
<a id="remove_gallery" href="javascript:void(0)">{t}Remove{/t}</a><br/>
{t}Current gallery{/t}: <a id="current_gallery_title" href="$html->url('/galleries/view/id:'){$object.gallery_id}">{$object.Gallery.title|default:''|escape:'html'}</a>
</p>
</fieldset>
{else}
{t}No galleries found{/t}
{/if}
</div>