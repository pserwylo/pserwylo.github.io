---
layout: post
title: A comprehensive set of tips for generating PDFs in the browser
description: 
summary: 
tags: software-dev javascript cognizant
minute: 1
---

2022-01-13

## Summary

Your web app has users, sometimes they just want a PDF copy of what they are looking at on the screen.

Sure, they could copy and paste the URL, you could provide a "share" button, they could bookmark the page, etc.
However, what happens when:
 * The content at the URL changes?
 * They want to attach it to an email?
 * They need an offline copy for archival purposes?
 * They want a hard copy?

When building a PDF export feature for a webapp, you are typically faced with a few options:
* Generate the PDF on the backend and serving to the browser (e.g. [PrinceXML](https://www.princexml.com/), [itext](https://github.com/itext/itext7), etc)
* Dynamically create a PDF in JavaScript (e.g. [JsPDF](https://github.com/parallax/jsPDF), [react-pdf](https://github.com/diegomura/react-pdf), etc)
* Write the DOM to a canvas and include as an image in the resulting PDF (e.g. using [html2canvas](https://html2canvas.hertzen.com/) + JsPDF)
* Prompting the user to save page as a PDF via the browser print dialog (by calling `window.print()`)

There are plenty of great articles [elaborating on these options](https://www.smashingmagazine.com/2019/06/create-pdf-web-application/).

## Generating PDFs in the browser using `window.print`

The rest of this article will focus on utilising the browser print dialog, and some gotcha's that you should consider if doing so.

<span style="color: darkgreen">**The primary reason**</span> to opt for this approach is that you can benefit from a single code base for both displaying information to users from the web browser as well as exporting a PDF.

<span style="color: darkred">**The main pitfall**</span> is that you have much less control over exactly when page-breaks will appear, and in general less control over the layout of the resulting PDF.

We will consider all of the following:
 * [Making the print dialog as transparent as possible to the users](#overall-user-experience) (so it feels as much a part of your application UX rather than part of the browser chrome)
 * [Adding headers + footers to each page](#headers-and-footers)
 * [Fitting complex user generated content to your PDF page](#fitting-user-generated-content) (in particular tables + images) that we have little or no control over (not withstanding XSS)
 * Ensuring all styles and images are loaded prior to initiating a print
 * Page sizing
 * Page breaking (and tables/headers)
 * Isolating styles from the application
 * Managing cross browser quirks

### Overall user experience

A naive approach would be: "Just ask the user to print the page".

This experience is a sub-par for many reasons, especially since browsers dropped the notion of a standard "File -> Print" menu in favour of weird little hamburger mystery menus some time ago, making it less likely users actually knwo where the print functionality is found.

Instead, we can trivially invoke `window.print()` from JavaScript to trigger the same functionality.
This should be done by a nice noticable button somewhere in your web app.

**TODO: do and don't images**. Don't is screenshot of hamburger menus and "Print" option. Do is a styled button in the webpage (with icon).

If, for reasons we will discuss below, we opt to render content into a new window for printing, then this button should:
 * Trigger `window.print()`
 * Call `window.open()` and render into the new window
 * Size the window to almost hide it behind the modal print dialog
 * Listen to `window.onafterprint` and close the window when done

The new window can be made almost transparent by sizing it such that the only thing the user sees is the print dialog.
Without appropriate sizing, users may get confused as to why they are seeing two previews of the content (one from the underlying webpage DOM, and one from the print preview).

**TODO: do and don't images**. Don't is a window too large showing two previews, Do is a popup covered by the print dialog.

### Render into a new window

The promise of [CSS media queries](https://developer.mozilla.org/en-US/docs/Web/CSS/Media_Queries/Using_media_queries#targeting_media_types) is that we can print a complex web app with only changes to CSS.

Sometimes, this just doesn't cut it and we need much more control, such as:
 * Specific HTML structures to aid with headers and footers
 * Manually manipulating DOM nodes to fit on the screen appropriately

These can be destructive operations that break the web app. For these reasons, it can be helpful to render your DOM for printing into a new window that can be disposed of once printed.

We still get to share rendering code, e.g. by rendering the same React components used by your web app into the new window, but we have the freedom of mangling it as we see fit to lay out appropriately on a the page. 

### Headers and footers

This will be simple once [CSS Paged Media Module Level 3](https://www.w3.org/TR/css-page-3/) is ratified and made avaialble in major browsers.
Until then, we are stuck with ugly workarounds.

**Sidenote:** There *are* rendering engines which understand advanced CSS Paged Media declarations, such as PrinceXML.
However our goal is to utilise the users browser, which means as of writing, we don't have access to these features.

**Goals:**
 * Show content repeated across the top and bottom of each page
 * Support artibrary number of pages (we normally don't know how many pages there will be in the resulting document)
 * Prevent obstructing any actual content behind our headers/footers

After searching around, the best approach is embodied in [this blog post](https://medium.com/@Idan_Co/the-ultimate-print-html-template-with-header-footer-568f415f6d2a) and variations appear throughout stackoverflow too.

**Solution:**

 * Add 2 x `position: fixed` elements for the header and footer respectively, and each will be repeated on each page by the browser when printing.
 * To prevent them overlapping other content, reserve space at the top and bottom of each page.
 * Reserve space by warpping content in a `<table>` where the `<thead>` and `<tfoot>` have a fixed height and are repeated on each page, and the content is within a `<tbody>`

**HTML**

```
<table>
  <thead>
    <tr>
      <td class="header-space">
        <!-- Empty. Just to reserve space for the <header> -->
      </td>
    </tr>
  </thead>

  <tbody>
    <tr>
      <td>
        <!-- Actual page content goes here -->
      </td>
    </tr>
  </tbody>

  <tfoot>
    <tr>
      <td class="footer-space">
        <!-- Empty. Just to reserve space for the <footer> -->
      </td>
    </tr>
  </tfoot>
</table>

<header class="header-content">
  <!-- Actual header content goes here -->
</header>

<footer class="footer-content">
  <!-- Actual header content goes here -->
</footer>
```

**CSS**

```
.header-content, .header-space,
.footer-content, .footer-space {
  height: 100px;
}

.header {
  position: fixed;
  top: 0;
}

.footer {
  position: fixed;
  bottom: 0;
}
```

Hopefully you can already see why it may be a good idea to render your printable pages in a new window.
Although possible to use the above HTML in your regular website layout, it will quickly start to cause issues, and we are just getting started!

### Fitting user generated content

Many systems that require exporting PDFs are enterprise systems, often with user generated content.
Such content is normally pasted into a WYSIWYG editor from MS Word.

<span style="color: darkred">**Security Warning:**</span> We will leave the notion of [protecting against XSS attacks](https://duckduckgo.com/?q=wysiwyg+prevent+xss) for another day.

**Goals:**
 * Fit complex tables, large images, and weird `div`s with fixed and large widths to our PDF pages (yes, these do get pasted regularly from weird and wonderful sources by users).

**Assumptions:**
 * We don't know the structure of these user generated tables.
 * We can't mess with the layout of the tables (often in enterprise systems, the table layouts can have semantics which change as we resize and shuffle columns around).

One potential solution is actually implemented by webkit itself, called the ["shrink factor"](https://github.com/WebKit/WebKit/blob/4f53761ae00714855680bf0b1be0c3e4e442aae8/Source/WebCore/page/PrintContext.cpp#L189-L202).
It is transparent to web developers, but the print dialog will attempt to shrink the entire document until it fits, but give up after a certain point and then truncate the rest of the content off the page.

We will build something similar in JavaScript, but instead of shrinking the entire document (unneccesarily making all text small and harder to read), we will just resize the problematic elements.

**Solution:**
 * Figure out how many pixels wide the resulting document will be (depends on the DPI of the current users screen).
 * Find all `<table>` (and potentially `<img>` or other) nodes are bigger than this.
 * Resize these until they are no larger than the page width.
 * Allow word breaking to prevent large strings of characters from disappearing

```

```

**Use `word-break: all` to prevent long strings of characters from flowing off the page.**

Without this, large lines of text such as `aaaaaaaaaaaaaaaaaaaaaaa...` will end up off the page. By allowing the browser to wrap words midway through, we trade off the chance of breaking some words in inopportune places with the benefit of *actually getting all our content on the page*.

**Find all tables which wider than the PDF page, and shrink them.**


Gotchas
 * Windows defaults to Microsoft Print to PDF. This outputs images. Chrome's is better.
