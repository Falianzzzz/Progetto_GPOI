(function () {
  var STORAGE_KEY = 'theme';
  var ATTR = 'data-theme';
  var DARK = 'dark';
  var LIGHT = 'light';
  var root = document.documentElement;

  function getSaved() {
    try { return localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
  }

  function setSaved(val) {
    try { localStorage.setItem(STORAGE_KEY, val); } catch (e) {}
  }

  function applyTheme(theme) {
    if (theme === LIGHT) {
      root.setAttribute(ATTR, LIGHT);
    } else {
      root.removeAttribute(ATTR);
    }
  }

  function toggleTheme() {
    var current = root.getAttribute(ATTR) === LIGHT ? DARK : LIGHT;
    applyTheme(current);
    setSaved(current);
  }

  var saved = getSaved();
  if (saved === LIGHT) {
    applyTheme(LIGHT);
  }

  document.addEventListener('click', function (e) {
    var btn = e.target.closest('.theme-toggle');
    if (btn) toggleTheme();
  });
})();
