<div class="bodybg">
    <div id="loadUsersInModal" class="loader"><span></span></div>
    <div id="listUsers">
        {include file='./inc/form_cards_to_assoc.tpl'}
    </div>
</div>

<div class="modalcommands">
    <input id="cardToUser" style="margin-bottom:10px;" type="button" value=" {t}associate{/t} "/>
    <input id="createCard" style="margin: 0 0 10px 10px" type="button" value=" {t}create new user{/t} "/>
</div>