{assign var='method' value=$view->action|default:'index'}

{*
Template incluso.
Menu a SX valido per tutte le pagine del controller.
*}

{if !empty($view->action) && $view->action == "view"}
<script type="text/javascript">
	var urlView = '{$html->url("/multimedia/view/")}' ;

	$(document).ready(function() { 

		$("#collision").hide();
		var optionsForm = { 
			error:		showResponse,  // post-submit callback  
			success:		showResponse,  // post-submit callback  
			dataType:		'html',        // 'xml', 'script', or 'json' (expected server response type)
			url: "{$html->url('/multimedia/saveAjax')}"
		} ;
	
		$("div.insidecol input[name='saveMedia']").click(function() { 
			if ( $('#concurrenteditors #editorsList').children().size() > 0 ) { 
				var answer = confirm("{t}More users are editing this object. Continue?{/t}");
			    if (answer) { 
			    	$(".secondacolonna .modules label").addClass("submitForm");
			    	$('#updateForm').ajaxSubmit(optionsForm);
			    } 
			} else if ( $('.publishingtree input:checked').val() === undefined ) {	
				var answer = confirm("{t}This content is not on publication tree. Continue?{/t}");
			    if (answer) { 
			    	$(".secondacolonna .modules label").addClass("submitForm");
			    	$('#updateForm').ajaxSubmit(optionsForm);
			    } 
			} else { 
		    	$(".secondacolonna .modules label").addClass("submitForm");
				$('#updateForm').ajaxSubmit(optionsForm);
			} 
    		return false;  
		} );

	} );

	function showResponse(data) { 
		$("#collision").html(data);
		$("#collision").show();
	} 
</script>
{/if}

<div class="secondacolonna {if !empty($fixed)}fixed{/if}">
	
	{if !empty($view->action) && $view->action != "index"}
		{assign var="back" value=$session->read("backFromView")}
	{else}
		{assign_concat var="back" 1=$html->url('/') 2=$currentModule.url}
	{/if}

	<div class="modules">
		<label class="{$moduleName}" rel="{$back}">{t}{$currentModule.label}{/t}</label>
	</div> 
	
	{if !empty($view->action) && $view->action != "index"}
	<div class="insidecol">
		<input class="bemaincommands" type="button" value=" {t}Save{/t} " name="saveMedia" id="saveBEObject" />
		<input class="bemaincommands" type="button" value=" {t}clone{/t} " name="clone" id="cloneBEObject" />
		<input class="bemaincommands" type="button" value="{t}Delete{/t}" name="delete" id="delBEObject" />
	</div>
	
		{$view->element('prevnext')}
	
	{/if}

	{assign var='cat' value=$categorySearched|default:''}

	{if $view->action == "index"}
		<ul class="menuleft insidecol catselector">
			<li><a href="javascript:void(0)" onClick="$('#mediatypes').slideToggle();">{t}Select by type{/t}</a></li>
				<ul id="mediatypes" {if empty($categorySearched)}style="display:none"{/if}>
					
					{foreach from=$conf->mediaTypes item="media_type"}
					<li class="ico_{$media_type} {if $cat==$media_type}on{/if}" rel="{$html->url('/multimedia')}/index/category:{$media_type}">
						{$media_type}
					</li>
					{/foreach}
					<li class="ico_all" rel="{$html->url('/multimedia')}">
						All
					</li>
				
				</ul>
		</ul>
	{/if}	



</div>