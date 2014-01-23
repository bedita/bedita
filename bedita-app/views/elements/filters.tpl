{bedev}
{* 
	<!--
	filters tab in list objects. Included in 
	/documebnts/index.tpl
	/multimedia/index.tpl
-->

*}

<div class="tab"><h2>{t}filters{/t}</h2></div>
<div>
	{$view->element("filters_form",[
	'filters' => [
		'word' => true, 
		'tree' => true,
		'treeDescendants' => false,
		'type' => true,
		'language' => true,
		'customProp' => false
	]])}
</div>
{/bedev}