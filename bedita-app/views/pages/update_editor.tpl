
    
{if $editors|@count > 1}

    <script type="text/javascript">
        if ((autoSaveTimer !== false) && (autoSaveTimer != undefined)) {
            switchAutosave("off", false);
        } else {
            switchAutosave("off");
        }

        $(".secondacolonna .modules label:not(.concurrentuser)")
        .addClass("concurrentuser")
        .prop("title", "Warning! More users are editing this document")
        .tooltip({
            extraClass: "tip",
            fixPNG: true,
            top: 10,
            left: -90

        });
    </script>

    {t}Warning{/t}.<br/>
    {t}Concurrent editors:{/t}

    <!-- <img src="{$html->url('/')}img/iconConcurrentuser.png" style="float:left; vertical-align:middle; width:20px; margin-right:10px;" /> -->

    <ul id="editorsList" style="margin-bottom:10px">
    {foreach from=$editors item="item"}
        <li rel="{$item.User.id}" style="border-bottom:1px solid gray">
            <b>{$item.User.realname|default:$item.User.userid|escape}</b>
        </li>
    {/foreach}
    </ul>
    
{else}

    <script type="text/javascript">
    if (autoSaveTimer === false) {
        var newStatus = $("input[name=data\\[status\\]]:checked").val();
        if ((status != 'on') && (status == newStatus))
            switchAutosave("on");
    }
    $(".secondacolonna .modules label").removeClass("concurrentuser").tooltip({
        position: { my: "center top+25", at: "center center" },
        delay: 0,
        show: {
            durate: 100,
            delay: 0
        },
        hide: {
            durate: 100,
            delay: 0
        }
    });
    </script>
{/if}