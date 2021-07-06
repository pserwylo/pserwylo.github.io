---
layout: none
---

//
// Adapted from https://github.com/danvk/danvk.github.io/commit/f99fa0d6ef808a2ba468587d3f7eab800d448f1e
//
// This attempts to emulate the 301 redirect behavior of
// the old /wp/index.php. It's a crappy solution, since it requires JS and
// thus leaves most bots SOL.
//

var redirects = {
  '?p=191': '/2013/08/12/make-your-writing-beautiful',
  '?p=128': '/2012/12/11/osdc-2012-building-a-freeish-mame-cabinet',
  '?p=115': '/2012/01/16/linux-conf-2012-finding-vulnerabilities-in-php-code',
  '?p=99': '/2011/11/21/osdc-2011-wii-homebrew-running-and-writing-software-for-the-wii',
  '?p=74': '/2011/08/01/phd-confirmation-seminar',
  '?p=60': '/2010/01/23/lca-2010-attribute-oriented-programming-in-php',
  '?p=41': '/2010/12/30/osdc-2010-bad-visualisation',
  '?p=29': '/2011/05/22/iscram-2011-phd-colloquium',
  '?p=1': '/2011/05/19/iscram-2011-predicting-patient-rates',
};

(function() {
  var s = document.location.search;
  if (s in redirects) {
    window.location.replace('{{ site.base_url }}' + redirects[s]);
  }
})();
