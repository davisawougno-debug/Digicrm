/**
 * Chef de Projet - AJAX Navigation
 * SPA-style sidebar navigation for Chef de Projet dashboard.
 * Matches the pattern used in admin.js and commercial.js.
 */

(function () {
  'use strict';

  var chefInitCalled = false;

  function initChefModules() {
    if (chefInitCalled) return;
    chefInitCalled = true;
    // Any chef-specific module initializers go here (Kanban, calendar etc.)
  }

  /**
   * Load a page via AJAX and replace admin-main content.
   * Preserves the sidebar active state and updates the URL.
   */
  function loadPage(url) {
    var separator = url.indexOf('?') === -1 ? '?' : '&';
    var ajaxUrl = url + separator + 'ajax=1';

    // Update active sidebar item
    var links = document.querySelectorAll('.sidebar-list a');
    var moduleMatch = url.match(/module=([a-z_-]+)/);
    var baseModule = moduleMatch ? moduleMatch[1] : 'dashboard';
    links.forEach(function (link) {
      var href = link.getAttribute('href');
      var linkModule = href.match(/module=([a-z_-]+)/);
      var isActive = linkModule && linkModule[1] === baseModule;
      link.closest('.sidebar-item').classList.toggle('active', isActive);
    });

    fetch(ajaxUrl)
      .then(function (res) {
        if (!res.ok) throw new Error('Erreur ' + res.status);
        return res.text();
      })
      .then(function (html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var newContent = doc.querySelector('.admin-main');
        var currentContent = document.querySelector('.admin-main');
        if (newContent && currentContent) {
          currentContent.innerHTML = newContent.innerHTML;
        }

        // Re-execute inline scripts in the new content
        var scripts = currentContent.querySelectorAll('script');
        scripts.forEach(function (oldScript) {
          var newScript = document.createElement('script');
          if (oldScript.src) {
            newScript.src = oldScript.src;
          } else {
            newScript.textContent = oldScript.textContent;
          }
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });

        initChefModules();

        // Update browser URL without full reload
        if (window.history && window.history.pushState) {
          window.history.pushState({ url: url, module: baseModule }, '', url);
        }
      })
      .catch(function (err) {
        console.error('AJAX load error:', err);
        window.location.href = url;
      });
  }

  // Intercept clicks on sidebar links
  document.addEventListener('click', function (e) {
    var link = e.target.closest('.sidebar-list a');
    if (!link) return;

    var href = link.getAttribute('href');
    // Only intercept internal chef_projet links
    if (!href || href.indexOf('/chef_projet/') === -1) return;

    e.preventDefault();
    loadPage(href);
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', function (e) {
    if (e.state && e.state.url) {
      loadPage(e.state.url);
    }
  });

  // Initialise on first load
  document.addEventListener('DOMContentLoaded', function () {
    initChefModules();
  });

})();
