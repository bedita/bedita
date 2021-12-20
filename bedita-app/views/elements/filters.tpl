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

{*
    - if you want to customize the label for categories pass an array('label' => 'mylabel') instead of true
    - if you want to customize the label for statuses pass an array('on' => 'myOnLabel', 'draft' => 'myDraftLabel, 'off' => 'myOffLabel) instead of true
      [if you don't want to see one of the status from the filer menu don't put it in the array]

    - 'append' is used to add custom HTML 'before' and/or 'after' core filters.
*}

{$defaultOptions = [
    'word' => true,
    'tree' => true,
    'treeDescendants' => true,
    'relations' => true,
    'type' => false,
    'language' => true,
    'user' => true,
    'customProp' => ['showObjectTypes' => false],
    'categories' => true,
    'mediaTypes' => false,
    'url' => $html->url([
        'controller' => $view->params.controller,
        'action' => $view->params.action
    ]),
    'tags' => true,
    'status' => false,
    'editorialContents' => true,
    'append' => [
        'before' => '',
        'after' => ''
    ]
]}

{$options = array_merge($defaultOptions, $options|default:[])}

<div class="tab{if $view->SessionFilter->check()} open filteractive{/if}"><h2>{t}filters{/t}</h2></div>
<div id="filterView">
    {$view->element("filters_form", ['filters' => $options])}
</div>