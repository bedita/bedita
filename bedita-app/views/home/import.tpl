{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}

<script type="text/javascript">
    $(document).ready(function() {
        openAtStart("#data-source, #data-options");
        $('.import-file input[type=file]').prop( "disabled", true );
        $('.import input[type=submit]').prop( "disabled", true );

        $('.import-type input[type=radio]').click(function(e) {
            $('.import-file input[type=file]').prop( "disabled", false );
            var sel = $(".import-type input[type='radio']:checked").val();
            $('#data-options>div').hide();
            $('#' + sel + '-options').show();
            console.log(sel);
        });

        $('.import-file input[type=file]').change(function() {
            if ($('.import-file input[type=file]').val()) {
                $('.import input[type=submit]').prop( "disabled", false );
            }
        });
        $.datepicker.setDefaults({
		speed: 'fast', 
		showOn: 'both',
		closeAtTop: false, 
		buttonImageOnly: true, 
	    buttonImage: '{$html->webroot}img/iconCalendar.gif', 
	    buttonText: '{t}Open Calendar{/t}',
	    dateFormat: '{$conf->dateFormatValidation|replace:'yyyy':'yy'}',
		firstDay: 1,
		nextText: "&rsaquo;&rsaquo;",
		prevText: "&lsaquo;&lsaquo;",
	    beforeShow: customRange
	}, $.datepicker.regional['{$currLang}']);
        $("input.dateinput").datepicker();
    });
</script>


{$view->element('modulesmenu')}
{include file = './inc/menuleft.tpl'}
{include file = './inc/menucommands.tpl'}


<div class="head">
    <h1>{t}Import data{/t}</h1>
</div>

<div class="mainfull import">
<form action="{$html->url('/home/importData')}" method="post" name="importData" enctype="multipart/form-data">
{$beForm->csrf()}

    <div class="mainhalf">

        <div class="tab stayopen"><h2>{t}Source data{/t}</h2></div>
        <fieldset id="data-source">
            <div class="import-type">
                {t}Import file type:{/t}
                <ul>
                    {foreach $filters as $filterType => $filter}
                    <li>
                        <input name="data[type]" type="radio" value="{$filterType}" id="select-{$filterType}">
                        <label for="select-{$filterType}">{$filter.label|default:$filterType}</label> &nbsp;
                    </li>
                    {/foreach}
                </ul>
            </div>

            <div class="import-file">
                {t}Select file{/t}:
                <input type="file" name="Filedata" disabled />
            </div>

            <div class="import-button-container">
                <input type="submit" value=" load " disabled />
            </div>
        </fieldset>

    </div>


    <div class="mainhalf">
        <div class="tab stayopen"><h2>{t}Import options{/t} <span style="display: none">for {$filter.label}</span></h2></div>

        <fieldset id="data-options">
            <div>{t}Select an import filter in Source data{/t}</div>

            {foreach $filters as $filterType => $filter}
            <div id="{$filterType}-options" style="display: none;">

            {if !empty($filter.options)}

                {foreach $filter.options as $optionName => $option}
                <div class="filter-option">
                    <p>{$option.label|default:$optionName}:</p>

                    {if $option.dataType == 'boolean'}

                        <input type="checkbox" name="data[{$optionName}]" value="{$optionName}" id="{$optionName}" {if !empty($option.defaultValue)}checked="checked"{/if}>

                    {elseif $option.dataType == 'date'}

                        <input type="text" name="data[{$optionName}]" id="{$optionName}" value="{if !empty($option.defaultValue)}{$option.defaultValue|date_format:$conf->datePattern}{/if}" size="10" class="dateinput" />

                    {elseif $option.dataType == 'number'}

                        <input type="text" name="data[{$optionName}]" id="{$optionName}" value="{$option.defaultValue|default:''}" size="12" class="numberinput" />

                    {elseif $option.dataType == 'text'}

                        <input type="text" name="data[{$optionName}]" id="{$optionName}" value="{$option.defaultValue|default:''}" size="40" class="textinput" />

                    {elseif $option.dataType == 'options'}

                        {* if number of options is > 3 use a select *}
                        {if count($option.values) > 3}
                            <select name="data[{$optionName}]" {if !empty($option.multipleChoice)}multiple{/if}>
                                {if empty($option.mandatory)}
                                    <option>--</option>
                                {/if}
                                {foreach $option.values as $optionValue => $optionLabel}
                                    <option value="{$optionValue}" {if !empty($option.defaultValue) && ($option.defaultValue == $optionValue)}selected="selected"{/if}>{$optionLabel}</option>
                                {/foreach}
                            </select>
                        {else}
                            <ul>
                                {foreach $option.values as $optionValue => $optionLabel}
                                <li>
                                    <input type="{if !empty($option.multipleChoice)}checkbox{else}radio{/if}"
                                        name="data[{$optionName}]" value="{$optionValue}" id="{$optionName}-{$optionValue}"
                                        {if !empty($option.defaultValue) && ($option.defaultValue == $optionValue)}checked="checked"{/if}>
                                    <label for="{$optionName}-{$optionValue}">{$optionLabel}</label>
                                </li>
                                {/foreach}
                            </ul>
                        {/if}

                    {elseif $option.dataType == 'tree'}
                        <select id="areaSectionAssoc" class="areaSectionAssociation" name="data[parent_id]" {if !empty($option.multipleChoice)}multiple{/if}>
                            {if empty($option.mandatory)}
                                <option>--</option>
                            {/if}
                            {if !empty($option.defaultValue)}
                                {$beTree->option($tree, $option.defaultValue)}
                            {else}
                                {$beTree->option($tree)}
                            {/if}
                        </select>
                    {/if}
                </div>
                {/foreach}

                {*
                TO DO da gestire via JS {$option.mandatory}

                {$option.defaultValue}
                {$option.multipleChoice}
                *}

            {else}
                <div class="filter-option">
                    <p>{$filter.label|default:$filterType} {t}filter has no options{/t}</p>
                </div>
            {/if}

            </div>
            {/foreach}

        </fieldset>

        {* SAMPLE OPTIONS <!--
            <select id="areaSectionAssoc" class="areaSectionAssociation" name="data[destination]">
                <option>--</option>
                <option>Selezione della sezione dell'albero in cui importare</option>
                {$beTree->option($tree)}
            </select>
            <hr />
            <input type="checkbox" checked="true" /> include media
            <hr/>
            {t}Status{/t}: {html_radios name="data[status]" options=$conf->statusOptions selected=$object.status|default:$conf->defaultStatus separator="&nbsp;"}
            
            <div id="finalimport" style="display:none; padding:10px 0px 10px 0px; margin:10px 0px 10px 0px; border-top:1px solid gray">
                <input type="submit" style="padding:10px" value="start import" />
            </div>
        --> *}
    </div>
</form>
</div>

