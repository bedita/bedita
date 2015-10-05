{if isset($moduleList.tags)}

<script type="text/javascript">
<!--
$(document).ready(function(){
	
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{$html->url('/tags/listAllTags')}", function() {
				$("#loadingTags").slideUp("fast");
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("{t}Hide system tags{/t}");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
				$("#callTags").text("{t}Show system tags{/t}");
			} else {
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("{t}Hide system tags{/t}");
			}
			showTags = !showTags;
		}
	});	
});
//-->
</script>

{$relcount = $object.Tag|@count|default:0}
<div class="tab"><h2 {if empty($relcount)}class="empty"{/if}>{t}Tags{/t} &nbsp; {if $relcount > 0}<span class="relnumb">{$relcount}</span>{/if}</h2></div>
<fieldset id="tags">

	<label>{t}add comma separated words{/t}:</label>
	<br/>
	
	{strip}
	<textarea name="tags" class="autogrowarea" style="display:block; margin-bottom:10px; width:470px" id="tagsArea">
	{if !empty($object.Tag)}
		{foreach from=$object.Tag item="tag" name="ft"}
			{$tag.label|escape}{if !$smarty.foreach.ft.last}, {/if}
		{/foreach}
	{/if}
	</textarea>
	{/strip}
	
	<a class="BEbutton" id="callTags" href="javascript:void(0);">
		{t}Show system tags{/t}
	</a>
	
	<div id="loadingTags" class="generalLoading" title="{t}Loading data{/t}">&nbsp;</div>
	
	<div id="listExistingTags" class="tag graced" style="display: none; margin-top:5px; text-align:justify;"></div>

</fieldset>

{/if}
