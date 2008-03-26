{literal}
<script type="text/javascript">
<!--
$(document).ready(function(){
	var showTagsFirst = false;
	var showTags = false;
	$("#callTags").bind("click", function() {
		if (!showTagsFirst) {
			$("#loadingTags").show();
			$("#listExistingTags").load("{/literal}{$html->url('/tags/listAllTags')}{literal}", function() {
				$("#loadingTags").hide();
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("{/literal}{t}Hide system tags{/t}{literal}");
				showTagsFirst = true;
				showTags = true;
			});
		} else {
			if (showTags) {
				$("#listExistingTags").slideUp("fast");
				$("#callTags").text("{/literal}{t}Show system tags{/t}{literal}");
			} else {
				$("#listExistingTags").slideDown("fast");
				$("#callTags").text("{/literal}{t}Hide system tags{/t}{literal}");
			}
			showTags = !showTags;
		}
	});
});
//-->
</script>
{/literal}

<h2 class="showHideBlockButton">{t}Tags{/t}</h2>
<div class="blockForm" id="tags">
	<fieldset>
	{t}Add comma separated words{/t}<br/>
	{strip}
	<textarea name="tags" id="tagsArea" rows="2" cols="50">
	{if !empty($object.ObjectCategory)}
		{foreach from=$object.ObjectCategory item="tag" name="ft"}
			{$tag.label}{if !$smarty.foreach.ft.last},&nbsp;{/if}
		{/foreach}
	{/if}
	</textarea>
	{/strip}
	<br/>
	<a id="callTags" href="javascript: void(0);">{t}Show system tags{/t}</a>
	</fieldset>
	<div id="loadingTags" class="loading">{t}Loading data{/t}...</div>
	<div id="listExistingTags" style="display: none;"></div>
</div>

