<fieldset id="selectcard">
    <table class="indexlist">
        <thead>
            <tr>
                <th class="header"></th>
                <th class="header">{t}name{/t}</th>
                <th class="header" style="width: 50%">{t}emails{/t}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$cards item=card}
            <tr>
                <td style="text-align: right"><input type="radio" class="cardradio" value="{$card.id}" name="cardtoassociate"/></td>

                {$name = '<i>[No title]</i>'}
                {if !empty($card.title)}{$name = $card.title|escape}{/if}
                {if !empty($card.name) || !empty($card.surname)}{$name = $card.name|cat:' '|cat:$card.surname|escape}{/if}
                <td>{$name}</td>

                {$emailUrl = 'mailto:'|cat:$card.email}{$emailUrl2 = 'mailto:'|cat:$card.email2}
                <td>
                    {if !empty($card.email)}{$html->link($card.email, $emailUrl)}{/if}
                    {if !empty($card.email) && !empty($card.email2)}, {/if}
                    {if !empty($card.email2)}{$html->link($card.email2, $emailUrl2)}{/if}
                </td>
            </tr>
        {foreachelse}
            <tr>
                <td colspan="3">
                    <i>{t}No results found{/t}</i>
                    <script type="text/javascript">
                        (function () {
                            // No results found. Automatically proceed with new card creation.
                            $("#createCard").click();
                        })();
                    </script>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</fieldset>
