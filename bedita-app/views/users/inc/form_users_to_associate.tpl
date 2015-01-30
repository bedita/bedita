<fieldset id="selectuser">
    <table class="indexlist">
        <thead>
            <tr>
                <th class="header"></th>
                <th class="header">{t}username{/t}</th>
                <th  class="header" style="width:50%">{t}realname{/t}</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$users item=u}
            <tr>
                <td style="text-align:right"><input type="checkbox" class="ucheck" value="{$u.User.id}" rel="{$u.User.userid|escape}" name="usertoassociate"/></td>
                <td>{$u.User.userid|escape}</td>
                <td>{$u.User.realname|escape}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</fieldset>

{* pagination *}
<div id="user_contents_nav" class="graced" 
style="text-align:center; color:#333; font-size:1.1em;  margin:25px 0px 1px 0px; background-color:#FFF; padding: 5px 10px 10px 10px;">
    
    {$paginator->counter(['format' => '%count%'])} {t}items{/t} | {t}page{/t} {$paginator->current()} {t}of{/t} {$paginator->counter(['format' => '%pages%'])}

    {if $paginator->hasPrev()}
        &nbsp; | &nbsp;
        <span><a href="javascript:void(0);" rel="1" id="streamFirstPage" title="{t}first page{/t}">{t}first{/t}</a></span>

        &nbsp; | &nbsp;
        <span><a href="javascript:void(0);" rel="{$paginator->current() - 1}" id="streamFirstPage" title="{t}previous page{/t}">{t}prev{/t}</a></span>
    {/if}

    {if $paginator->hasNext()}
        &nbsp; | &nbsp;
        <span><a href="javascript:void(0);" rel="{$paginator->current() + 1}" id="streamNextPage" title="{t}next page{/t}">{t}next{/t}</a></span>

        &nbsp; | &nbsp;
        <span><a href="javascript:void(0);" rel="{$paginator->counter(['format' => '%pages%'])}" id="streamNextPage" title="{t}last page{/t}">{t}last{/t}</a></span>
    {/if}

</div>