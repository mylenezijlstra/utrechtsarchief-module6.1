// assets/js/panorama.js
// Publieke panorama viewer: hotspots positioneren op basis van data-pos-* (px),
// info-boxen onder hotspots plaatsen en full-width, scrollbare mini-map.

document.addEventListener('DOMContentLoaded', () => {

  /* -------------------------
     Helpers: update hotspots per wrapper
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
          // zet CSS custom properties zodat bestaande CSS positioning werkt
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
     Plaats info-box precies onder hotspot (px-positie binnen wrapper)
     ------------------------- */
  function placeBoxUnderHotspot(hotEl, boxEl) {
    if (!hotEl || !boxEl) return;
    const wrapper = hotEl.closest('.image-wrapper');
    if (!wrapper) return;

    const wrapRect = wrapper.getBoundingClientRect();
    const hotRect = hotEl.getBoundingClientRect();

    // center x van hotspot
    const hotCenterX = hotRect.left + hotRect.width / 2;
    // top net onder hotspot (hotRect.bottom relatief aan wrapper)
    const topPx = Math.round(hotRect.bottom - wrapRect.top + 8); // kleine marge
    // center box horizontaal op hotspot center
    const leftPx = Math.round(hotCenterX - wrapRect.left);

    // gebruik inline styles zodat CSS transform translateX(-50%) centreert
    boxEl.style.left = leftPx + 'px';
    boxEl.style.top = topPx + 'px';

    // als box heeft caret onderkant, zorg dat caret zichtbaar; laat CSS ::before ongewijzigd
    boxEl.style.display = 'block';
    boxEl.classList.add('show');
    boxEl.hidden = false;
  }

  /* -------------------------
     Kopieer positie van hotspot naar box (fallback)
     ------------------------- */
  function copyHotspotVarsToBox(hotspot, box) {
    if (!hotspot || !box) return;

    // probeer eerst CSS custom properties op hotspot
    const cs = getComputedStyle(hotspot);
    const left = cs.getPropertyValue('--hotspot-left').trim();
    const top  = cs.getPropertyValue('--hotspot-top').trim();

    if (left && top) {
      // zet box CSS vars zodat bestaande CSS kan positioneren (oude fallback)
      box.style.setProperty('--hotspot-left', left);
      box.style.setProperty('--hotspot-top', top);
      return;
    }

    // fallback: als geen CSS vars, gebruik dataset px -> bereken %
    if (hotspot.dataset.posTop && hotspot.dataset.posLeft) {
      const wrapper = hotspot.closest('.image-wrapper');
      const img = wrapper ? wrapper.querySelector('img') : null;
      if (img) {
        const rect = img.getBoundingClientRect();
        const rawTop = parseFloat(hotspot.dataset.posTop);
        const rawLeft = parseFloat(hotspot.dataset.posLeft);
        if (!isNaN(rawTop) && !isNaN(rawLeft) && rect.width > 0 && rect.height > 0) {
          box.style.setProperty('--hotspot-left', (rawLeft / rect.width * 100) + '%');
          box.style.setProperty('--hotspot-top', (rawTop / rect.height * 100) + '%');
          return;
        }
      }
    }

    // laatste fallback: center
    box.style.setProperty('--hotspot-left', '50%');
    box.style.setProperty('--hotspot-top', '50%');
  }

  /* -------------------------
     Close helper
     ------------------------- */
  function closeAllBoxes(wrapper) {
    wrapper.querySelectorAll('.info-box').forEach(b => {
      b.style.display = 'none';
      b.classList.remove('show');
      b.hidden = true;
      // clear inline left/top to avoid stale positions
      b.style.left = '';
      b.style.top = '';
    });
    wrapper.querySelectorAll('.hotspot').forEach(h => h.setAttribute('aria-expanded', 'false'));
  }

  /* -------------------------
     Init per wrapper
     ------------------------- */
  function initWrapper(wrapper) {
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
     Klik / toggle handler: plaats box onder hotspot
     ------------------------- */
  document.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (!hotspot) return;
    const wrapper = hotspot.closest('.image-wrapper');
    if (!wrapper) return;

    // sluit alle boxes in deze wrapper
    closeAllBoxes(wrapper);

    // zoek box: id match via data-target of data-target attribute
    const targetId = hotspot.dataset.target || hotspot.getAttribute('data-target');
    let box = null;
    if (targetId) box = wrapper.querySelector('#' + CSS.escape(targetId));
    if (!box) {
      // fallback: find info-box with matching data-extra-id (for admin-like markup)
      const extraId = hotspot.dataset.extraId || hotspot.getAttribute('data-extra-id');
      if (extraId) {
        box = Array.from(wrapper.querySelectorAll('.info-box')).find(b => String(b.dataset.extraId || b.getAttribute('data-extra-id')) === String(extraId));
      }
    }
    if (!box) {
      // last fallback: nearest .info-box sibling
      box = hotspot.nextElementSibling && hotspot.nextElementSibling.classList.contains('info-box') ? hotspot.nextElementSibling : null;
    }
    if (!box) return;

    // plaats box onder hotspot (px-positie)
    placeBoxUnderHotspot(hotspot, box);
    hotspot.setAttribute('aria-expanded', 'true');

    // focus op eerste focusbaar element in box
    const focusable = box.querySelector('button, [tabindex], input, textarea, a');
    if (focusable) focusable.focus();
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
     Mini-map: vullen en sync (full-width, scrollable track)
     ------------------------- */
  function populateAndInitMiniMap() {
    const panorama = document.querySelector('.panorama');
    const mini = document.querySelector('.mini-map');
    if (!panorama || !mini) return;

    // Zorg inner en track bestaan
    let inner = mini.querySelector('.mini-inner');
    if (!inner) {
      inner = document.createElement('div');
      inner.className = 'mini-inner';
      // verplaats bestaande children (zoals mini-highlight) in inner als nodig
      while (mini.firstChild) inner.appendChild(mini.firstChild);
      mini.appendChild(inner);
    }

    let track = inner.querySelector('.mini-track');
    if (!track) {
      track = document.createElement('div');
      track.className = 'mini-track';
      // verplaats highlight indien aanwezig
      const existingHighlight = inner.querySelector('.mini-highlight');
      if (existingHighlight) track.appendChild(existingHighlight);
      inner.insertBefore(track, inner.firstChild);
    }

    // Clear bestaande thumbnails (behoud highlight)
    let highlight = track.querySelector('.mini-highlight');
    if (!highlight) {
      highlight = document.createElement('div');
      highlight.className = 'mini-highlight';
      track.appendChild(highlight);
    }
    Array.from(track.querySelectorAll('img.mini-thumb')).forEach(n => n.remove());

    const wrappers = Array.from(panorama.querySelectorAll('.image-wrapper'));
    if (wrappers.length === 0) return;

    // thumbnail breedte (in px) uit CSS variabele of fallback
    const css = getComputedStyle(document.documentElement);
    const thumbWidthStr = css.getPropertyValue('--mini-thumb-width') || '140px';
    const thumbWidth = parseInt(thumbWidthStr, 10) || 140;

    // Maak thumbnails en voeg data-index toe
    wrappers.forEach((wrap, idx) => {
      const img = wrap.querySelector('img');
      if (!img) return;
      const thumb = document.createElement('img');
      thumb.className = 'mini-thumb';
      thumb.src = img.src;
      thumb.alt = 'thumb ' + (idx + 1);
      thumb.dataset.index = idx;
      thumb.style.flex = `0 0 ${thumbWidth}px`;
      thumb.style.width = `${thumbWidth}px`;
      thumb.style.height = '100%';
      thumb.style.objectFit = 'cover';
      thumb.style.cursor = 'pointer';
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

    // Update highlight positie/width op basis van panorama scroll
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

    // Update thumbnail selection and ensure visible thumb when panorama scrolls
    function updateThumbSelection() {
      const panRect = panorama.getBoundingClientRect();
      const thumbs = Array.from(track.querySelectorAll('img.mini-thumb'));
      thumbs.forEach((thumb, idx) => {
        const wrap = wrappers[idx];
        if (!wrap) return;
        const rect = wrap.getBoundingClientRect();
        const mid = rect.left + rect.width / 2;
        if (mid >= panRect.left && mid <= panRect.right) {
          thumb.style.outline = '2px solid rgba(43,122,120,0.9)';
          // scroll thumb into view inside track if needed
          const thumbRect = thumb.getBoundingClientRect();
          const trackRect = track.getBoundingClientRect();
          if (thumbRect.left < trackRect.left + 8) {
            track.scrollBy({ left: thumbRect.left - trackRect.left - 8, behavior: 'smooth' });
          } else if (thumbRect.right > trackRect.right - 8) {
            track.scrollBy({ left: thumbRect.right - trackRect.right + 8, behavior: 'smooth' });
          }
        } else {
          thumb.style.outline = 'none';
        }
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

    setTimeout(() => { updateHighlight(); updateThumbSelection(); }, 150);
  }

  populateAndInitMiniMap();

  console.log('panorama.js initialized. Hotspots:', document.querySelectorAll('.hotspot').length);
});
