{* esempio di stile per la sitemap *}
<style type="text/css">
{literal}
	ul#sitemap { border: solid blue 1px; }
	ul.contents { border: solid green 1px; }
	li.section, li.Section {color:#626151 !important;}
	li.Document {color:#ff6600 !important;}
	li.Gallery {color:#FFBF00 !important;}
	li.Event {color:#0099CC !important;}
	li.Newsletter {color:#6666FF !important;}
	li.Image, li.Audio, li.Video {color:#ff0033 !important;}
	li.Tag {color:#336666 !important;}
	li.Comment {color:#F08080 !important;}
	li.Addressbook, li.Card {color:#009933 !important;}
	li.News, .ShortNews {color:#003366 !important;}
	li.Book {color:#996633 !important;}
	li.Bibliography {color:#999966 !important;}
	li.Ecommerce {color:#771717 !important;}
	li.Webmark {color:#6600cc !important;}
	li.Form {color:#801f2b !important;}
	li.Booking {color:#99CC33 !important;}
	li.Faq {color:#CC3333 !important;}
	li.Print {color:#CC9900 !important;}
	li.Topography {color:#993300 !important;}
	li.Statistic, li.Stat {color:#788279 !important;}
{/literal}
</style>
{$beTree->sitemap($sections_tree)}