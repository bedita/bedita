{*
**  inline javascript for <head> section
**
**
*}

	{* table rows color *}
	{if empty($moduleColor) || $moduleName eq 'admin'}
		{assign var="moduleColor" value="#BBBBBB"}
	{/if}

	<script language="JavaScript" type="text/javascript">
		$(document).ready(function() {
			var trHoverColor = "{$moduleColor}";
			
			$('TABLE.indexList TR.rowList').hover (
				function() {
					$(this).css ( { 'background-color': trHoverColor, });
				}, function() {
					$(this).css ( { 'background-color': "" });
				});
		});
	</script>