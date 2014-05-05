{$html->script('libs/jquery/plugins/jquery.float_thead.min.js', false)}

<script type="text/javascript">
<!--
$(document).ready(function(){

	$('.indexlist').each(function() {
        $(this)
            .width( $(this).closest('.mainfull, .main').outerWidth() )
            .floatThead();
    });
})
//-->
</script>

<table class="indexlist">
	<thead>
		<tr>
			<th>{t}name{/t}</th>
			<th>{t}publication{/t}</th>
			<th>{t}sender{/t}</th>
			<th>Id</th>
		</tr>
	</thead>

	{if !empty($objects)}		
	{foreach from=$objects item="template"}

	<tr rel="{$html->url('/newsletter/viewMailTemplate/')}{$template.id}">

		<td>
			{$template.title}
		</td>
		<td>
			{$template.Area.title}							
		</td>
		<td>
			{$template.sender|default:null}
		</td>
		<td>
			{$template.id}
		</td>
	</tr>
	
	
{/foreach}
{else}
	
	<tr><td colspan="100" style="padding: 30px;">{t}No templates found{/t}</td></tr>
	
{/if}

</table>