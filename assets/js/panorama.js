// assets/js/panorama.js
document.addEventListener('DOMContentLoaded', () => {

  /* -------------------------
     Helpers: bereken & zet hotspots
     ------------------------- */
  function updateHotspotsForWrapper(wrapper) {
    const img = wrapper.querySelector('img');
    if (!img) return;

    function apply() {
      const rect = img.getBoundingClientRect();
      const imgWidth = rect.width;
      const imgHeight = rect.height;

      wrapper.querySelectorAll('.hotspot').forEach(h => {
        // dataset px-waarden (raw px binnen originele afbeelding)
        const rawTop = h.dataset.posTop !== undefined ? parseFloat(h.dataset.posTop) : NaN;
        const rawLeft = h.dataset.posLeft !== undefined ? parseFloat(h.dataset.posLeft) : NaN;

        if (!isNaN(rawTop) && !isNaN(rawLeft) && imgWidth > 0 && imgHeight > 0) {
          // bereken percentage t.o.v. weergegeven afbeelding afmetingen
          const topPct = (rawTop / imgHeight) * 100;
          const leftPct = (rawLeft / imgWidth) * 100;
          h.style.setProperty('--hotspot-top', topPct + '%');
          h.style.setProperty('--hotspot-left', leftPct + '%');
          h.style.display = 'flex';
          return;
        }

        // fallback: inline style met --hotspot-top/left in %
        const inline = h.getAttribute('style') || '';
        const matchTop = inline.match(/--hotspot-top\s*:\s*([0-9.]+%)/);
        const matchLeft = inline.match(/--hotspot-left\s*:\s*([0-9.]+%)/);
        if (matchTop && matchLeft) {
          h.style.setProperty('--hotspot-top', matchTop[1]);
          h.style.setProperty('--hotspot-left', matchLeft[1]);
          h.style.display = 'flex';
          return;
        }

        // geen data: verberg of zet op 0%
        h.style.setProperty('--hotspot-top', '0%');
        h.style.setProperty('--hotspot-left', '0%');
      });
    }

    if (img.complete) apply();
    else img.addEventListener('load', apply, { once: true });

    const onResize = () => apply();
    window.addEventListener('resize', onResize);
    wrapper._panoramaResizeHandler = onResize;
  }

  /* -------------------------
     Kopieer hotspot CSS-variabelen naar de info-box
     ------------------------- */
  function copyHotspotVarsToBox(hotspot, box) {
    if (!hotspot || !box) return;

    // probeer eerst CSS custom properties op hotspot
    const cs = getComputedStyle(hotspot);
    const left = cs.getPropertyValue('--hotspot-left').trim();
    const top  = cs.getPropertyValue('--hotspot-top').trim();

    if (left) box.style.setProperty('--hotspot-left', left);
    if (top)  box.style.setProperty('--hotspot-top', top);

    // fallback: als geen CSS vars, gebruik dataset px -> bereken %
    if ((!left || !top) && hotspot.dataset.posTop && hotspot.dataset.posLeft) {
      const wrapper = hotspot.closest('.image-wrapper');
      const img = wrapper ? wrapper.querySelector('img') : null;
      if (img) {
        const rect = img.getBoundingClientRect();
        const rawTop = parseFloat(hotspot.dataset.posTop);
        const rawLeft = parseFloat(hotspot.dataset.posLeft);
        if (!isNaN(rawTop) && !isNaN(rawLeft) && rect.width > 0 && rect.height > 0) {
          box.style.setProperty('--hotspot-left', (rawLeft / rect.width * 100) + '%');
          box.style.setProperty('--hotspot-top', (rawTop / rect.height * 100) + '%');
        }
      }
    }

    // laatste fallback: als box nog geen vars heeft, zet center
    const finalLeft = box.style.getPropertyValue('--hotspot-left') || '';
    const finalTop  = box.style.getPropertyValue('--hotspot-top') || '';
    if (!finalLeft) box.style.setProperty('--hotspot-left', '50%');
    if (!finalTop)  box.style.setProperty('--hotspot-top', '50%');
  }

  /* -------------------------
     Close helper
     ------------------------- */
  function closeAllBoxes(wrapper) {
    wrapper.querySelectorAll('.info-box').forEach(b => {
      b.hidden = true;
      b.classList.remove('show');
    });
    wrapper.querySelectorAll('.hotspot').forEach(h => h.setAttribute('aria-expanded', 'false'));
  }

  /* -------------------------
     Init per wrapper
     ------------------------- */
  function initWrapper(wrapper) {
    // bereken hotspots
    updateHotspotsForWrapper(wrapper);

    // focusable & aria
    wrapper.querySelectorAll('.hotspot').forEach((h, i) => {
      if (!h.hasAttribute('tabindex')) h.setAttribute('tabindex', '0');
      if (!h.dataset.target) h.dataset.target = 'hotspot-target-' + i;
      h.setAttribute('role', 'button');
      h.setAttribute('aria-expanded', 'false');
    });
  }

  // initialiseer alle wrappers
  document.querySelectorAll('.image-wrapper').forEach(initWrapper);

  /* -------------------------
     Klik / toggle handler
     ------------------------- */
  document.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (!hotspot) return;
    const wrapper = hotspot.closest('.image-wrapper');
    if (!wrapper) return;

    const targetId = hotspot.dataset.target;
    if (!targetId) return;

    // zoek box: id match of data-extra-id match
    let box = wrapper.querySelector('#' + CSS.escape(targetId));
    if (!box) {
      // fallback: find info-box with matching data-extra-id
      const extraId = hotspot.dataset.extraId || hotspot.getAttribute('data-extra-id');
      if (extraId) {
        box = Array.from(wrapper.querySelectorAll('.info-box')).find(b => String(b.dataset.extraId || b.getAttribute('data-extra-id')) === String(extraId));
      }
    }
    if (!box) return;

    const wasVisible = !box.hidden && box.classList.contains('show');

    // sluit alle boxes in deze wrapper
    closeAllBoxes(wrapper);

    if (!wasVisible) {
      // kopieer positie van hotspot naar box zodat box precies onder die hotspot opent
      copyHotspotVarsToBox(hotspot, box);

      box.hidden = false;
      box.classList.add('show');
      hotspot.setAttribute('aria-expanded', 'true');

      // focus op eerste focusbaar element in box
      const focusable = box.querySelector('button, [tabindex], input, textarea, a');
      if (focusable) focusable.focus();
    } else {
      hotspot.setAttribute('aria-expanded', 'false');
    }
  });

  /* -------------------------
     Klik buiten sluit (per wrapper)
     ------------------------- */
  document.addEventListener('click', (e) => {
    const wrapper = e.target.closest('.image-wrapper');
    if (!wrapper) {
      // klik buiten alle wrappers: sluit alles
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
      return;
    }
    // klik binnen wrapper maar niet op hotspot of info-box -> sluit in die wrapper
    const isHotspot = !!e.target.closest('.hotspot');
    const isBox = !!e.target.closest('.info-box');
    if (!isHotspot && !isBox) closeAllBoxes(wrapper);
  }, true);

  /* -------------------------
     Keyboard support
     ------------------------- */
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      const active = document.activeElement;
      if (!active) return;
      const hotspot = active.closest ? active.closest('.hotspot') : null;
      if (!hotspot) return;
      e.preventDefault();
      hotspot.click();
    } else if (e.key === 'Escape') {
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
    }
  });

  /* -------------------------
     Mini-map: vullen en sync
     ------------------------- */
  function populateAndInitMiniMap() {
    const panorama = document.querySelector('.panorama');
    const mini = document.querySelector('.mini-map');
    if (!panorama || !mini) return;

    // Zorg dat mini-track bestaat
    let track = mini.querySelector('.mini-track');
    if (!track) {
      track = document.createElement('div');
      track.className = 'mini-track';
      mini.insertBefore(track, mini.firstChild);
    }

    // Clear bestaande thumbnails (veilig herladen)
    track.innerHTML = '';
    const wrappers = Array.from(panorama.querySelectorAll('.image-wrapper'));
    if (wrappers.length === 0) return;

    // Maak thumbnails (kleine img elementen) en voeg data-index toe
    wrappers.forEach((wrap, idx) => {
      const img = wrap.querySelector('img');
      if (!img) return;
      const thumb = document.createElement('img');
      thumb.className = 'mini-thumb';
      thumb.src = img.src;
      thumb.alt = 'thumb ' + (idx + 1);
      thumb.dataset.index = idx;
      thumb.style.width = (100 / wrappers.length) + '%';
      thumb.style.height = '100%';
      thumb.style.objectFit = 'cover';
      thumb.style.display = 'inline-block';
      thumb.style.cursor = 'pointer';
      thumb.style.opacity = '0.9';
      thumb.style.borderRadius = '4px';
      thumb.style.marginRight = '4px';
      thumb.addEventListener('click', () => {
        const target = wrappers[idx];
        if (!target) return;
        const panRect = panorama.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const offset = targetRect.left - panRect.left + panorama.scrollLeft;
        panorama.scrollTo({ left: Math.max(0, offset), behavior: 'smooth' });
      });
      track.appendChild(thumb);
    });

    // Zorg dat highlight bestaat
    let highlight = mini.querySelector('.mini-highlight');
    if (!highlight) {
      highlight = document.createElement('div');
      highlight.className = 'mini-highlight';
      track.appendChild(highlight);
    }

    function updateHighlight() {
      const totalW = panorama.scrollWidth;
      const viewW = panorama.clientWidth;
      const scrollX = panorama.scrollLeft;
      if (totalW <= 0) return;
      const ratio = viewW / totalW;
      const leftPct = (scrollX / totalW) * 100;
      const widthPct = Math.max(ratio * 100, 2);
      highlight.style.left = leftPct + '%';
      highlight.style.width = widthPct + '%';
    }

    function updateThumbSelection() {
      const panRect = panorama.getBoundingClientRect();
      wrappers.forEach((wrap, idx) => {
        const thumb = track.querySelector('img[data-index="' + idx + '"]');
        if (!thumb) return;
        const rect = wrap.getBoundingClientRect();
        const mid = rect.left + rect.width / 2;
        if (mid >= panRect.left && mid <= panRect.right) thumb.style.outline = '2px solid rgba(43,122,120,0.9)';
        else thumb.style.outline = 'none';
      });
    }

    panorama.addEventListener('scroll', () => { updateHighlight(); updateThumbSelection(); }, { passive: true });
    window.addEventListener('resize', () => { updateHighlight(); updateThumbSelection(); });

    const imgs = panorama.querySelectorAll('img');
    let loaded = 0;
    if (imgs.length === 0) { updateHighlight(); updateThumbSelection(); }
    imgs.forEach(img => {
      if (img.complete) { loaded++; if (loaded === imgs.length) { updateHighlight(); updateThumbSelection(); } }
      else img.addEventListener('load', () => { loaded++; updateHighlight(); updateThumbSelection(); });
    });

    setTimeout(() => { updateHighlight(); updateThumbSelection(); }, 120);
  }

  populateAndInitMiniMap();

  console.log('panorama.js initialized. Hotspots:', document.querySelectorAll('.hotspot').length);
});
