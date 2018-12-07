{if in_array($object.object_type_id, [$conf->objectTypes['audio']['id'], $conf->objectTypes['video']['id']])}
<div class="tab"><h2>{t}Captions{/t}</h2></div>
<div id="captions">
    {foreach $object.captions as $caption}
    <div class="caption-item">
        <label>{t}title{/t}:</label>
        <input name="data[captions][{$caption@index}][title]" value="{$caption.title|default:''}" type="text" />
        <div class="caption-meta">
            <select name="data[captions][{$caption@index}][lang]">
                {foreach $conf->langOptions as $code => $name}
                    <option value="{$code}" {if $code == $caption.lang}selected{/if}>{$name}</option>
                {/foreach}
            </select>
            <input type="hidden" name="data[captions][{$caption@index}][id]" value="{$caption.id|default:''}" />
            {foreach ['on', 'draft', 'off'] as $status}
                <label>
                    <input type="radio" name="data[captions][{$caption@index}][status]" value="{$status}" {if $status == $caption.status}checked{/if} />
                    {t}{$status}{/t}
                </label>
            {/foreach}
        </div>
        <label style="margin-top: 1em;">webvtt:</label>
        <textarea name="data[captions][{$caption@index}][description]">{$caption.description|default:''}</textarea>
        <output></output>
        <button data-delete="{$caption@index}">{t}delete{/t}</button>
    </div>
    {/foreach}
    <button data-add>{t}add{/t}</button>
    <div class="caption-item" style="display: none;">
        <label>{t}title{/t}:</label>
        <input name="data[captions][{$caption@total + 1}][title]" type="text" />
        <div class="caption-meta">
            <select name="data[captions][{$caption@total + 1}][lang]">
                {foreach $conf->langOptions as $code => $name}
                    <option value="{$code}" {if $code == $conf->defaultLang}selected{/if}>{$name}</option>
                {/foreach}
            </select>
            <input type="hidden" name="data[captions][{$caption@total + 1}][id]" />
            {foreach ['on', 'draft', 'off'] as $status}
                <label>
                    <input type="radio" name="data[captions][{$caption@total + 1}][status]" value="{$status}" {if $status == $conf->defaultStatus}checked{/if} />
                    {t}{$status}{/t}
                </label>
            {/foreach}
        </div>
        <label>webvtt:</label>
        <textarea name="data[captions][{$caption@total + 1}][description]"></textarea>
        <output></output>
    </div>
</div>
{$html->script('libs/webvtt/parser', true)}
<script>
(function() {
    var parser = new WebVTTParser();
    var VALID_LABEL = "✔︎ {t}valid{/t}";
    var INVALID_LABEL = "✘ {t}invalid{/t}";

    function validate(event) {
        var value = $(this).val();
        var info = parser.parse(value);

        if (info.errors && info.errors.length) {
            $(this).next('output')
                .removeClass('valid')
                .addClass('invalid')
                .html(INVALID_LABEL + '<ul>' + info.errors.map(function(error) {
                    return '<li>' + error.message + ' (line ' + error.line + ')</li>';
                }).join('\n') + '</ul>');
            return;
        }
        $(this).next('output')
            .removeClass('invalid')
            .addClass('valid')
            .html(VALID_LABEL);
    }

    $('.caption-item textarea').on('input', validate).on('change', validate);

    $('#captions [data-add]').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).hide()
        $(this).next().slideDown();
    });

    $('.caption-item [data-delete]').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        $(this).parent().remove();
    });
}());
</script>
{/if}
