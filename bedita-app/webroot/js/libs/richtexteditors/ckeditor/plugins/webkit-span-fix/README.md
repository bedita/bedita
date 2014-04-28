Plugin for CKEditor
===============

http://ckeditor.com

About
-----------------
Plugin ```webkit-span-fix``` been created to fix bug, which appears when merging paragraphs in CKEditor with delete/backspace.

Basic Information:
-----------------

* Webkit will insert/update span Elements in the short time when the keyDown and the keyUp event is triggered
* Webkit updates ALL Elements within the last block element that is being merged within another (Block Elements are all element with display:block as computed style

What plugin does:
-----------------

* When the keyDown event is fired, all Element- or text-nodes-siblings directly after the selection (or cursor) within (direct children, not children-chilren..) the last merged block element are saved in an array -> They are not affected at this time of the webkit-span-update
* Directly after the keyDown event, the keyUp event is bound to the editor once. When the keyUp event is fired (and webkit has already updated the spans) the same thing as in step 1 will be done with the new selection, and all siblings are saved to another array
* Before the fix will start to replace or remove Elements from the array of step 1 with the array of step 2, it will find out if 2 block elements have been merged when the whole keypress is done
* If so, a difference is being created from the step1-array and step2-array. If a element in the step2-array is a span, but the element on the same index in the step1-array is not a span, the span will be removed and replaced with its contents; if a element in the step2-array is a span with styles in the style-attribute, and the element on the same index in the step1-array is also a span with style-attribute, but the style differs, the style-Element is being copied from the step1-array-element to the step2-array-element.

Full Bug description
---------------
Original: [http://dev.ckeditor.com/ticket/9998](http://dev.ckeditor.com/ticket/9998?cversion=1&cnum_hist=45#comment:45)

> Occurs in Chrome v24.0.1312.56 / CKEditor 4.0.1 (revision d02739b) / CKEditor 4 DEV (Standard) (revision d02739b) (nightly build demo)
> Did not occur in Firefox v17.0.1
> When you have a paragraph with several lines of text, ie.:

```
<p>line1<br />
line2</p>
```

> and want to create 2 separate paragraphs, you could go with your cursor to the end of line1, press ENTER to create a new paragraph and press DELETE to remove the whiteline caused by the BR tag. Then CKEditor puts some HTML in a SPAN tag with a line-height styling.

```html
<p>line1</p>
<p><span style="line-height: 1.6em;">line2</span></p>
```

> Other examples: It also happens when trying to create a single line out of the next cases:

```html
<p><br />
line2</p>
```

> and

```html
<p>line1</p>
<p>line2</p>
```

Credits
===============
Author **pr0nbaer**, original code been taken from: [here](http://dev.ckeditor.com/ticket/9998?cversion=1&cnum_hist=45#comment:45) and http://pastebin.com/XUC7rCdn
