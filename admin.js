/**
 * DigiCRM Administration - JavaScript Principal
 * Interactions, AJAX, modals, charts, sidebar
 */

(function () {
  'use strict';

  // ============================================================
  // Sidebar
  // ============================================================
  const sidebar = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  function toggleSidebar() {
    if (!sidebar) return;
    sidebar.classList.toggle('sidebar--open');
    if (overlay) overlay.classList.toggle('sidebar--open');
  }

  if (toggleBtn) toggleBtn.addEventListener('click', toggleSidebar);
  if (overlay) overlay.addEventListener('click', toggleSidebar);

  // Auto-close sidebar on window resize (md breakpoint = 768px)
  window.addEventListener('resize', function () {
    if (window.innerWidth > 768 && sidebar) {
      sidebar.classList.remove('sidebar--open');
      if (overlay) overlay.classList.remove('sidebar--open');
    }
  });

  // ============================================================
  // AJAX Navigation (SPA)
  // ============================================================
  var adminMain = document.querySelector('.admin-main');
  var sidebarLinks = document.querySelectorAll('.sidebar-link:not(.sidebar-link--danger)');

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
        initModules();
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

  function initModules() {
    // Alerts auto-dismiss
    document.querySelectorAll('.admin-alert').forEach(function (alert) {
      setTimeout(function () {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s ease';
        setTimeout(function () {
          if (alert.parentNode) alert.remove();
        }, 300);
      }, 5000);
    });
    // Delete confirmation
    document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var url = this.getAttribute('data-delete-url');
        var msg = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';
        document.getElementById('deleteModalMessage').textContent = msg;
        document.getElementById('deleteModalConfirm').href = url;
        openModal('deleteModal');
      });
    });
    // Toggle switches
    document.querySelectorAll('.toggle-switch').forEach(function (el) {
      el.addEventListener('change', function () {
        this.closest('form')?.submit();
      });
    });
  }

  // Intercept sidebar link clicks
  sidebarLinks.forEach(function (link) {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      var url = this.getAttribute('href');
      loadPage(url, true);
    });
  });

  // Browser back/forward
  window.addEventListener('popstate', function (e) {
    if (e.state && e.state.url) {
      loadPage(e.state.url, false);
    }
  });

  // ============================================================
  // User Dropdown
  // ============================================================
  const userMenuBtn = document.getElementById('userMenuBtn');
  const userDropdown = document.getElementById('userDropdown');

  if (userMenuBtn && userDropdown) {
    userMenuBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle('navbar-dropdown--open');
    });

    document.addEventListener('click', function () {
      userDropdown.classList.remove('navbar-dropdown--open');
    });
    userDropdown.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }

  // ============================================================
  // Notifications Panel
  // ============================================================
  const notifBtn = document.getElementById('notifBtn');
  const notifPanel = document.getElementById('notifPanel');

  if (notifBtn && notifPanel) {
    notifBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      notifPanel.classList.toggle('notif-panel--open');
      if (userDropdown) userDropdown.classList.remove('navbar-dropdown--open');
    });

    document.addEventListener('click', function () {
      notifPanel.classList.remove('notif-panel--open');
    });
    notifPanel.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  }

  // ============================================================
  // Global Search (AJAX)
  // ============================================================
  const searchInput = document.getElementById('globalSearch');
  const searchResults = document.getElementById('searchResults');
  let searchTimeout;

  if (searchInput && searchResults) {
    searchInput.addEventListener('input', function () {
      clearTimeout(searchTimeout);
      const query = this.value.trim();

      if (query.length < 2) {
        searchResults.classList.remove('search-dropdown--open');
        searchResults.innerHTML = '';
        return;
      }

      searchTimeout = setTimeout(function () {
          fetch(BASE_URL + '/admin/index.php?module=search&q=' + encodeURIComponent(query))
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (data.length === 0) {
              searchResults.innerHTML = '<div class="notif-empty">Aucun résultat</div>';
              searchResults.classList.add('search-dropdown--open');
              return;
            }

            var html = '';
            data.forEach(function (item) {
              html += '<a href="' + item.url + '" class="dropdown-item" style="border-bottom:1px solid var(--gray-100)">';
              html += '<i class="fas fa-' + item.icon + '"></i>';
              html += '<div><strong>' + item.label + '</strong><br><small>' + item.sub + '</small></div>';
              html += '</a>';
            });
            searchResults.innerHTML = html;
            searchResults.classList.add('search-dropdown--open');
          })
          .catch(function () {
            searchResults.classList.remove('search-dropdown--open');
          });
      }, 300);
    });

    document.addEventListener('click', function (e) {
      if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.classList.remove('search-dropdown--open');
      }
    });
  }

  // ============================================================
  // Modals
  // ============================================================
  window.openModal = function (modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.add('modal--open');
  };

  window.closeModal = function (modalId) {
    var modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('modal--open');
  };

  // Close modal on backdrop click
  document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
    backdrop.addEventListener('click', function () {
      var modal = this.closest('.modal');
      if (modal) modal.classList.remove('modal--open');
    });
  });

  // Close modal on Escape key
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal--open').forEach(function (m) {
        m.classList.remove('modal--open');
      });
    }
  });

  // ============================================================
  // Delete confirmation via data-url attribute
  // ============================================================
  document.querySelectorAll('[data-delete-url]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var url = this.getAttribute('data-delete-url');
      var msg = this.getAttribute('data-message') || 'Êtes-vous sûr de vouloir supprimer cet élément ?';

      document.getElementById('deleteModalMessage').textContent = msg;
      document.getElementById('deleteModalConfirm').href = url;
      openModal('deleteModal');
    });
  });

  // ============================================================
  // Ligne management (Devis / Factures)
  // ============================================================
  var ligneIndex = 0;

  window.addLigne = function (containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var template = container.querySelector('.ligne-template');
    if (!template) return;

    var clone = template.cloneNode(true);
    clone.classList.remove('ligne-template');
    clone.style.display = '';
    clone.querySelectorAll('[name]').forEach(function (el) {
      el.name = el.name.replace('__INDEX__', ligneIndex);
      el.value = '';
    });
    clone.querySelector('.ligne-montant').textContent = '0';

    container.insertBefore(clone, template);

    // Add event listeners for auto-calc
    var qteInput = clone.querySelector('.ligne-qte');
    var puInput = clone.querySelector('.ligne-pu');
    if (qteInput && puInput) {
      function calcLigne() {
        var qte = parseFloat(qteInput.value) || 0;
        var pu = parseFloat(puInput.value) || 0;
        var total = qte * pu;
        var montantEl = clone.querySelector('.ligne-montant');
        if (montantEl) montantEl.textContent = total.toFixed(2);
        calcTotaux(containerId);
      }
      qteInput.addEventListener('input', calcLigne);
      puInput.addEventListener('input', calcLigne);
    }

    ligneIndex++;
  };

  window.removeLigne = function (btn) {
    var row = btn.closest('.ligne-row');
    if (row) {
      row.remove();
      var containerId = row.closest('[id]')?.id;
      if (containerId) calcTotaux(containerId);
    }
  };

  function calcTotaux(containerId) {
    var container = document.getElementById(containerId);
    if (!container) return;

    var rows = container.querySelectorAll('.ligne-row:not(.ligne-template)');
    var totalHT = 0;

    rows.forEach(function (row) {
      var montantEl = row.querySelector('.ligne-montant');
      if (montantEl) totalHT += parseFloat(montantEl.textContent) || 0;
    });

    var htInput = container.closest('form')?.querySelector('.total-ht');
    var ttcSpan = container.closest('form')?.querySelector('.total-ttc');
    var tvaInput = container.closest('form')?.querySelector('input[name="tva"]');

    if (htInput) htInput.value = totalHT.toFixed(2);

    if (tvaInput && ttcSpan) {
      var tva = parseFloat(tvaInput.value) || 0;
      var ttc = totalHT + (totalHT * tva / 100);
      ttcSpan.textContent = ttc.toFixed(2);
    }

    if (tvaInput) {
      tvaInput.addEventListener('input', function () { calcTotaux(containerId); });
    }
  }

  // Auto-init lignes calc on page load
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[id$="LignesContainer"]').forEach(function (c) {
      calcTotaux(c.id);
    });
  });

  // ============================================================
  // Toggle Switch (checkbox styling)
  // ============================================================
  document.querySelectorAll('.toggle-switch').forEach(function (el) {
    el.addEventListener('change', function () {
      this.closest('form')?.submit();
    });
  });

  // ============================================================
  // Alerts auto-dismiss
  // ============================================================
  document.querySelectorAll('.admin-alert').forEach(function (alert) {
    setTimeout(function () {
      alert.style.opacity = '0';
      alert.style.transition = 'opacity 0.3s ease';
      setTimeout(function () {
        if (alert.parentNode) alert.remove();
      }, 300);
    }, 5000);
  });

  // ============================================================
  // Export functions
  // ============================================================
  var BASE_URL = (typeof BASE_URL !== 'undefined') ? BASE_URL :
    window.location.origin + '/Digicrm';

  window.exportPDF = function (elementId, filename) {
    var element = document.getElementById(elementId);
    if (!element) {
      element = document.querySelector('.admin-main');
    }
    if (typeof html2pdf !== 'undefined') {
      html2pdf()
        .set({
          margin: 10,
          filename: filename || 'rapport-digicrm.pdf',
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { scale: 2, useCORS: true },
          jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
        })
        .from(element)
        .save();
    } else {
      window.print();
    }
  };

  window.exportExcel = function (tableId, filename) {
    var table = document.getElementById(tableId);
    if (!table) {
      table = document.querySelector('.admin-table');
    }
    if (typeof XLSX !== 'undefined' && table) {
      var wb = XLSX.utils.table_to_book(table, { sheet: 'Sheet1' });
      XLSX.writeFile(wb, filename || 'export-digicrm.xlsx');
    } else {
      alert('La bibliothèque Excel n\'est pas chargée.');
    }
  };

  // ============================================================
  // Tooltip init (simple)
  // ============================================================
  document.querySelectorAll('[data-tooltip]').forEach(function (el) {
    el.addEventListener('mouseenter', function () {
      var tip = document.createElement('div');
      tip.className = 'tooltip-box';
      tip.textContent = this.getAttribute('data-tooltip');
      tip.style.cssText =
        'position:absolute;background:var(--gray-800);color:#fff;padding:4px 8px;' +
        'border-radius:4px;font-size:12px;z-index:3000;pointer-events:none;';
      document.body.appendChild(tip);
      var rect = this.getBoundingClientRect();
      tip.style.left = (rect.left + rect.width / 2 - tip.offsetWidth / 2) + 'px';
      tip.style.top = (rect.top - tip.offsetHeight - 4) + 'px';
      el._tooltip = tip;
    });
    el.addEventListener('mouseleave', function () {
      if (this._tooltip) {
        this._tooltip.remove();
        this._tooltip = null;
      }
    });
  });

  // ============================================================
  // Dark Mode Toggle
  // ============================================================
  (function () {
    var themeToggle = document.getElementById('themeToggle');
    var html = document.documentElement;
    var icon = themeToggle ? themeToggle.querySelector('i') : null;
    var currentTheme = localStorage.getItem('digicrm-theme');

    function applyTheme(theme) {
      if (theme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        if (icon) { icon.className = 'fas fa-sun'; }
        if (themeToggle) themeToggle.title = 'Mode clair';
      } else {
        html.removeAttribute('data-theme');
        if (icon) { icon.className = 'fas fa-moon'; }
        if (themeToggle) themeToggle.title = 'Mode sombre';
      }
      localStorage.setItem('digicrm-theme', theme);
    }

    // Respect system preference if no stored theme
    if (!currentTheme) {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        currentTheme = 'dark';
      } else {
        currentTheme = 'light';
      }
    }
    applyTheme(currentTheme);

    if (themeToggle) {
      themeToggle.addEventListener('click', function () {
        var next = html.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        applyTheme(next);
      });
    }
  })();

})();
