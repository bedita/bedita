{* esempio di stile per la sitemap *}
<style type="text/css">
{literal}
	ul#sitemap { border: solid blue 1px; }
	ul.contents { border: solid green 1px; }
	li.Section a {color:#626151 !important;}
	li.Document a {color:#ff6600 !important;}
	li.Gallery a {color:#FFBF00 !important;}
	li.Event a {color:#0099CC !important;}
	li.Newsletter a {color:#6666FF !important;}
	li.Image a, li.Audio a, li.Video a {color:#ff0033 !important;}
	li.Tag a {color:#336666 !important;}
	li.Comment a {color:#F08080 !important;}
	li.Addressbook a, li.Card a {color:#009933 !important;}
	li.News a, .ShortNews a {color:#003366 !important;}
	li.Book a {color:#996633 !important;}
	li.Bibliography a {color:#999966 !important;}
	li.Ecommerce a {color:#771717 !important;}
	li.Webmark a {color:#6600cc !important;}
	li.Form a {color:#801f2b !important;}
	li.Booking a {color:#99CC33 !important;}
	li.Faq a {color:#CC3333 !important;}
	li.Print a {color:#CC9900 !important;}
	li.Topography a {color:#993300 !important;}
	li.Statistic a, li.Stat a {color:#788279 !important;}
{/literal}
</style>
{$beTree->sitemap($sections_tree,$public_url)}