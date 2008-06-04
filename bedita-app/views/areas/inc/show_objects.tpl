
<script type="text/javascript">
<!--
{literal}
$(document).ready(function(){
	$("#listObjToAssoc a").click(function(){
		var idObjAssoc = $(this).siblings("input[@name='idObjAssoc']").val();
		var relation = $("#selectRelationType").val();
		uploadItemById(idObjAssoc, relation);		
	});
});
{/literal}
//-->
</script>

{if $objectsToAssoc.items}
	<ul id="listObjToAssoc">
	{foreach from=$objectsToAssoc.items item="objToAss"}
		<li>
			<a href="javascript:void(0)" title="{t}add{/t}">{$objToAss.title}</a> ({t}{$objToAss.relation}{/t})
			<input type="hidden" name="idObjAssoc" value="{$objToAss.id}"/>
		</li>
	{/foreach}
	</ul>
{else}
	{t}No items in section{/t}
{/if}