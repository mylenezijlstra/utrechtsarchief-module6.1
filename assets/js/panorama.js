document.querySelectorAll('.hotspot').forEach(hotspot => {
  
  hotspot.addEventListener('click', () => {
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
