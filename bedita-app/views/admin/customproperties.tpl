{$html->css('module.superadmin')}
{$javascript->link("jquery/interface", false)}
{$javascript->link("form", false)}
{$javascript->link("jquery/jquery.form", false)}
{$javascript->link("jquery/jquery.cmxforms", false)}
{$javascript->link("jquery/jquery.metadata", false)}
{$javascript->link("jquery/jquery.validate", false)}
{$javascript->link("jquery/jquery.changealert", false)}
{$javascript->link("jquery/jquery.treeview", false)}



</head>

<body>

{include file="../common_inc/modulesmenu.tpl"}

{include file="inc/menuleft.tpl" method="customproperties"}

<div class="head">
	<div class="toolbar" style="white-space:nowrap">
	<h2>{t}Custom properties{/t}</h2>
	
	{include file="./inc/toolbar.tpl" label_items='custom properties'}
	</div>
</div>


{include file="inc/menucommands.tpl" method="customproperties" fixed=false}

<div class="mainfull">

	<table class="indexlist">
		<tr>
			<th>property</th>
			<th>data type</th>
			<th>object type</th>
		</tr>
		
		<tr>
			<td>
				<span class="listrecent {$conf->objectTypes[$ot].model|lower}">&nbsp;</span>
				{$conf->objectTypes[$ot].model}
			</td>
		</tr>
			
	</table>

</div>

