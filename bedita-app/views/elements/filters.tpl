{*
	override default filter options defining an array of options in view that calls this element
	i.e.

	{$view->element("filters", [
            'options' => [
                'type' => true,
                'url' => ...
            ]
        ]
    )}
*}

{$defaultOptions = [
		'word' => true,
		'tree' => true,
		'treeDescendants' => true,
        'relations' => true,
		'type' => false,
		'language' => true,
		'customProp' => ['showObjectTypes' => false],
		'categories' => true,
		'mediaTypes' => false,
		'url' => $html->url("/")|cat:$view->params.controller|cat:'/'|cat:$view->params.action,
		'tags' => false
	]
}

{$options = array_merge($defaultOptions, $options|default:[])}

<div class="tab{if $view->SessionFilter->check()} open filteractive{/if}"><h2>{t}filters{/t}</h2></div>
<div>
	{$view->element("filters_form", ['filters' => $options])}
</div>