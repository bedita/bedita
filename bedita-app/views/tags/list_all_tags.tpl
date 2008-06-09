{literal}
<script type="text/javascript">
	$(document).ready(function() {
		$("#listTags a").bind("click", function() {
			var sep = ", ";
			if ($("#tagsArea").val() == "") {
				sep = "";
			}
			// check if tag already exists in textarea
			var tagInTextArea = false;
			var words = $("#tagsArea").val().split(",");
			for (i=0; i<words.length; i++) {
				if (jQuery.trim(words[i]) == jQuery.trim($(this).text())) {
					var tagInTextArea = true;
					break;
				}
			}
			if (!tagInTextArea) {
				$("#tagsArea").val(
					$("#tagsArea").val() 
					+ sep 
					+ jQuery.trim($(this).text())
				);
			}
		});
	});
</script>
{/literal}

<div id="listTags">
{if !empty($listTags)}
	{foreach from=$listTags item="tag"}
		<a href="javascript: void(0)">{$tag.label}&nbsp;</a>
	{/foreach}
{else}
	{t}No tags found.{/t}
{/if}
</div>

<pre>
	{dump var=$listTags}
</pre>