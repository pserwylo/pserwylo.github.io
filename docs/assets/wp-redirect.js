//
// Adapted from https://github.com/danvk/danvk.github.io/commit/f99fa0d6ef808a2ba468587d3f7eab800d448f1e
//
// This attempts to emulate the 301 redirect behavior of
// the old /wp/index.php. It's a crappy solution, since it requires JS and
// thus leaves most bots SOL.
//

var redirects = {
  '?p=191': '/2013/08/12/make-your-writing-beautiful',
};

(function() {
  var s = document.location.search;
  if (s in redirects) {
    window.location.replace(redirects[s]);
  }
})();
