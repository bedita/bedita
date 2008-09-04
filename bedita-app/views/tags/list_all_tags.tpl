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

<div id="listTags" class="tag graced" style="text-align:justify; line-height:1.5em;">
{if !empty($listTags)}
	{foreach from=$listTags item="tag"}
		<a title="{$tag.weight}" class="{$tag.class|default:""}" href="{if !empty($href)}{$html->url('/tags/view/')}{$tag.id}{else}javascript: void(0);{/if}">{$tag.label}</a>
	{/foreach}
{else}
	{t}No tags found.{/t}
{/if}
</div>

				