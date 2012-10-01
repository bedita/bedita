{assign var="user" value=$this->Session->read('BEAuthUser')}
{assign var="submiturl" value=$submiturl|default:$currentModule.url}

<div data-role="page">

	<div data-role="header">
		<h1>{if !empty($object)}{$object.title|default:"<i>[no title]</i>"}{else}<i>[{t}New item{/t}]</i>{/if}</h1>
		<a href="{$this->Html->url('/')}{$submiturl}/index" data-icon="arrow-l" data-iconpos="notext">{t}Back{/t}</a>
	</div><!-- /header -->

	<div data-role="content">
		{$view->element('objects/form', [
			'user'=>$user,
			'submiturl'=>$submiturl
		])}
	</div>
	{$view->element('footer')}
</div>