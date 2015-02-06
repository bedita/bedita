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
                {$emailUrl = 'mailto:'|cat:$card.email}{$emailUrl2 = 'mailto:'|cat:$card.email2}
                <td style="text-align: right"><input type="radio" class="cardradio" value="{$card.id}" name="cardtoassociate"/></td>
                <td>{$card.name|escape} {$card.surname|escape}</td>
                <td>{$html->link($card.email, $emailUrl)}, {$html->link($card.email2, $emailUrl2)}</td>
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