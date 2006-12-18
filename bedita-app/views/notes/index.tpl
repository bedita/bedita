<h1>My Notes</h1>

<p>{$html->link('Add note', '/notes/add')}</p> 

<table>
   <tr>
       <th>Id</th>
       <th>Title</th>
       <th>&nbsp;</th>
	   <th>Created</th>
   </tr>
   {foreach item=note from=$notes}
   		{assign var="note" value=$note.Note}
		{assign_concat var="linkView" 	0="/notes/view/" 1=$note.id}
		{assign_concat var="linkEdit" 	0="/notes/edit/" 1=$note.id}
		{assign_concat var="linkDelete" 0="/notes/delete/" 1=$note.id}

   <tr>
       <td>{$note.id}</td>
       <td>
	   		{$html->link($note.title, $linkView)}
       </td>
       <td>
	   {$html->link($note.title, $linkView)}
     	[
		{$html->link("Edit", $linkEdit)}
		,
		{$html->link("Delete", $linkDelete, null, 'Are you sure?')}
		]
       </td> 	   

       <td>{$note.created}</td>
 </tr>
   {/foreach}
</table>


