{$html->script('form', false)}

{$html->script('libs/jquery/plugins/jquery.tablesorter.min',false)}

<script type="text/javascript">
    $(document).ready(function(){	
		openAtStart('#objects');
		$('.sortableTable').tablesorter( { sortList: [[1,1]]} ); 
    });
</script>

{$view->element('modulesmenu')}

{include file='inc/menuleft.tpl' method='index'}

{include file='inc/menucommands.tpl' method='index'}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	{if !empty($sectionSel)} 
		<h2>{$moduleName} for “ <span style="color:white" class="evidence">{$sectionSel.title|escape}</span> ”</h2>
	{/if}
	</div>
</div>

<div class="mainfull">

	<div class="tab"><h2>{t}BEdita contents statistics{/t}</h2></div>
	<div id="statsobjects">
		{include file='inc/objects.tpl'}

		{if !empty($conf->statistics['evolution'])}
			{include file='inc/evolution.tpl'}
		{/if}

		{if !empty($conf->statistics['comments'])}
			{include file='inc/comments.tpl'}
		{/if}

		{if !empty($conf->statistics['relations'])}
			{include file='inc/relations.tpl'}
		{/if}
	</div>

	{if !empty($conf->statistics['editors'])}
		{include file='inc/editors.tpl'}
	{/if}

	{if !empty($conf->statistics['publications'])}
		{include file='inc/publications.tpl'}
	{/if}

	{if !empty($conf->statistics['objectsusers'])}
		{include file='inc/users.tpl'}
	{/if}

</div>
