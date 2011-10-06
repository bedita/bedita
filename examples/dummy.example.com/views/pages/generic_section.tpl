<h3>Hi, this is a working BEdita frontend</h3>

Original:
{$beEmbedMedia->object($section.currentContent, ['presentation' => 'full'])}

Crop:
{$beEmbedMedia->object($section.currentContent, ['mode' => 'crop', 'width' => '200', 'heigth' => '200'])}

Crop Only:
{$beEmbedMedia->object($section.currentContent, ['mode' => 'croponly', 'modeparam' => 'TC' ,  'width' => '600'])}


Fill:
{$beEmbedMedia->object($section.currentContent, ['mode' => 'fill', 'modeparam' => '000000' , 'width' => '300', 'heigth' => '200'])}