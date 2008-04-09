{*
**  inline javascript for <head> section
**
**
*}

	{* table rows color *}
	{if empty($moduleColor) || $moduleName eq 'admin'}
		{assign var="moduleColor" value="#BBBBBB"}
	{/if}
	{literal}
	<script language="JavaScript" type="text/javascript">
		$(document).ready(function() {
			var trHoverColor = "{/literal}{$moduleColor}{literal}";
			
			$('TABLE.indexList TR.rowList').hover (
				function() {
					$(this).css ( { 'background-color': trHoverColor, });
				}, function() {
					$(this).css ( { 'background-color': "" });
				});
		});
	</script>
	{/literal}


	{* correctly handle PNG transparency in IE 5.5/6 - added by xho - remove this comment in future *}
	<!--[if lt IE 7]>
	<script defer type="text/javascript" src="js/pngfix_ielt7.js"></script>
	<![endif]-->



