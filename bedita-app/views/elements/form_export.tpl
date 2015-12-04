{$html->css("ui.datepicker", null, ['inline' => false])}
{$html->script("libs/jquery/ui/jquery.ui.datepicker.min", false)}
{if $currLang != "eng"}
{$html->script("libs/jquery/ui/i18n/jquery.ui.datepicker-$currLang2.min.js", false)}
{/if}

<script type="text/javascript">
    $(document).ready(function() {
        openAtStart("#data-source, #data-options");
        $('.export-type input[type=radio]').click(function(e) {
            var sel = $(".export-type input[type='radio']:checked").val();
            $('#data-options>div').hide();
            $('#' + sel + '-options').show();
            console.log(sel);
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
            }, $.datepicker.regional['{$currLang}']
        );
        $("input.dateinput").datepicker();
        $('#doExport').click(function(){
            var sel = $(".export-type input[type='radio']:checked").val();
            if (sel) {
                $('.exportoption').each(function(){
                    if (!($(this).hasClass(sel))) {
                        $(this).remove();
                    }
                });
                $('#updateForm').attr('action','{$html->url('/areas/export')}');
                $('#updateForm').submit();
            }
        });
    });
</script>

<div class="tab"><h2>{t}Export{/t}</h2></div>

<fieldset id="export">

    <div class="mainhalf" style="width:49%; margin-right:2%;">
        <fieldset id="data-source" class="ignore">
            <div class="export-type">
                <ul>
                    {foreach $export_filters as $filterType => $filter}
                    <li>
                        <input name="data[type]" type="radio" value="{$filterType}" class="export-type">
                        <label for="select-{$filterType}">{$filter.label|default:$filterType}</label> ({$filterType}) &nbsp;
                    </li>
                    {/foreach}
                </ul>
            </div>

            <div class="export-file" style="padding-top:10px;padding-bottom:10px;">
                {t}File{/t}:
                <input type="text" name="data[filename]" value="{$object.nickname|default:'bedita_export_'}" id="exportFileName" size="40">
            </div>

            <div class="export-button-container">
                <input type="button" value="{t}export{/t}" id="doExport" />
            </div>
        </fieldset>
    </div>

    <div class="mainhalf" style="width:49%; margin-right:0;">

        <fieldset id="data-options" class="ignore">
            {foreach $export_filters as $filterType => $filter}
            <div id="{$filterType}-options" style="display: none;margin-top:10px;">

            {if !empty($filter.options)}

                {foreach $filter.options as $optionName => $option}
                <div class="filter-option">
                    <p>{$option.label|default:$optionName}:</p>

                    {if $option.dataType == 'boolean'}

                        <input type="checkbox" name="data[options][{$optionName}]" value="{$optionName}" id="{$optionName}" {if !empty($option.defaultValue)}checked="checked"{/if} class="exportoption {$filterType}">

                    {elseif $option.dataType == 'date'}

                        <input type="text" name="data[options][{$optionName}]" id="{$optionName}" value="{if !empty($option.defaultValue)}{$option.defaultValue|date_format:$conf->datePattern}{/if}" size="10" class="dateinput exportoption {$filterType}" />

                    {elseif $option.dataType == 'number'}

                        <input type="text" name="data[options][{$optionName}]" id="{$optionName}" value="{$option.defaultValue|default:''}" size="12" class="numberinput exportoption {$filterType}" />

                    {elseif $option.dataType == 'text'}

                        <input type="text" name="data[options][{$optionName}]" id="{$optionName}" value="{$option.defaultValue|default:''}" size="40" class="textinput exportoption {$filterType}" />

                    {elseif $option.dataType == 'options'}

                        {* if number of options is > 3 use a select *}
                        {if count($option.values) > 3}
                            <select name="data[options][{$optionName}]" {if !empty($option.multipleChoice)}multiple{/if} class="exportoption {$filterType}">
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
                                        name="data[options][{$optionName}]" value="{$optionValue}" id="{$optionName}-{$optionValue}"
                                        {if !empty($option.defaultValue) && ($option.defaultValue == $optionValue)}checked="checked"{/if} class="exportoption {$filterType}">
                                    <label for="{$optionName}-{$optionValue}">{$optionLabel}</label>
                                </li>
                                {/foreach}
                            </ul>
                        {/if}

                    {elseif $option.dataType == 'tree'}
                        <select id="areaSectionAssoc" class="areaSectionAssociation exportoption {$filterType}" name="data[parent_id]" {if !empty($option.multipleChoice)}multiple{/if}>
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
            {/if}

            </div>
            {/foreach}

        </fieldset>
    </div>
</fieldset>