{*
** detail of media item
*}
{if isset($object) && !empty($object.uri) && $object.ObjectType.name == "image"}

    {$html->script('flatlander', false)}
    {$html->css('flatlander', false)}

    <div class="tab"><h2>{t}Advanced editor{/t}</h2></div>

    <fieldset id="advanced-multimedia-editor" style="margin-left:-10px;">

        {$params = [
                'width' => 600,
                'longside' => false,
                'mode' => 'fill',
                'modeparam' => '000000',
                'upscale' => false
            ]
        }
        {$beEmbedMedia->object($object, $params)}

    </fieldset>

{/if}