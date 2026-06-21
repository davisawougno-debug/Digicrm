/**
 * DigiCRM Commercial - JavaScript
 */

(function () {
  'use strict';

  var adminMain = document.querySelector('.admin-main');
  var sidebarLinks = document.querySelectorAll('.commercial-sidebar .sidebar-link:not(.sidebar-link--danger)');

  function loadPage(url, pushState) {
    pushState = pushState !== false;
    var separator = url.indexOf('?') > -1 ? '&' : '?';
    fetch(url + separator + 'ajax=1')
      .then(function (res) { return res.text(); })
      .then(function (html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var newMain = doc.querySelector('.admin-main');
        if (newMain && adminMain) {
          adminMain.outerHTML = newMain.outerHTML;
          adminMain = document.querySelector('.admin-main');
        }
        doc.querySelectorAll('script').forEach(function (oldScript) {
          var script = document.createElement('script');
          if (oldScript.src) {
            script.src = oldScript.src;
          } else {
            script.textContent = oldScript.textContent;
          }
          document.body.appendChild(script);
          document.body.removeChild(script);
        });
        if (pushState) {
          history.pushState({ url: url }, '', url);
        }
        updateActiveSidebar(url);
        initCommercialModules();
      })
      .catch(function () {
        window.location.href = url;
      });
  }

  function updateActiveSidebar(url) {
    var params = new URLSearchParams(url.split('?')[1] || '');
    var module = params.get('module') || 'dashboard';
    sidebarLinks.forEach(function (link) {
      var item = link.closest('.sidebar-item');
      if (!item) return;
      var linkParams = new URLSearchParams(link.getAttribute('href').split('?')[1] || '');
      var linkModule = linkParams.get('module') || 'dashboard';
      item.classList.toggle('active', linkModule === module);
    });
  }

  function initCommercialModules() {
    document.querySelectorAll('.admin-alert').forEach(function (alert) {
      setTimeout(function () {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease';
        setTimeout(function () {
          if (alert.parentNode) alert.remove();
        }, 300);
      }, 5000);
    });
    document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var url = this.getAttribute('data-delete-url');
        var msg = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
        document.getElementById('deleteModalMessage').textContent = msg;
        document.getElementById('deleteModalConfirm').href = url;
        if (window.openModal) window.openModal('deleteModal');
      });
    });
  }

  sidebarLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var url = this.getAttribute('href');
      loadPage(url, true);
    });
  });

  window.addEventListener('popstate', function (e) {
    if (e.state && e.state.url) {
      loadPage(e.state.url, false);
    }
  });

})();
