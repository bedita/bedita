{literal}
<script type="text/javascript">
	$(document).ready(function() {
		$("#listTags a").bind("click", function() {
			var sep = ", ";
			if ($("#tagsArea").text() == "") {
				sep = "";
			}
			$("#tagsArea").append(sep + jQuery.trim($(this).text()));
		});
	});
</script>
{/literal}

<div id="listTags">
{foreach from=$listTags item="tag"}
	<a href="javascript: void(0)">{$tag.label}&nbsp;</a>
{/foreach}
</div>