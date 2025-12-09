// assets/js/panorama.js
document.addEventListener('DOMContentLoaded', () => {

  /* --- Hotspot helpers --- */
  function updateHotspotsForWrapper(wrapper) {
    const img = wrapper.querySelector('img');
    if (!img) return;

    function apply() {
      const rect = img.getBoundingClientRect();
      const imgWidth = rect.width;
      const imgHeight = rect.height;

      wrapper.querySelectorAll('.hotspot').forEach(h => {
        const rawTop = parseFloat(h.dataset.posTop || 'NaN');
        const rawLeft = parseFloat(h.dataset.posLeft || 'NaN');
        if (!isNaN(rawTop) && !isNaN(rawLeft) && imgWidth > 0 && imgHeight > 0) {
          const topPct = (rawTop / imgHeight) * 100;
          const leftPct = (rawLeft / imgWidth) * 100;
          h.style.setProperty('--hotspot-top', topPct + '%');
          h.style.setProperty('--hotspot-left', leftPct + '%');
          h.classList.add('visible');
        } else {
          h.style.setProperty('--hotspot-top', '0%');
          h.style.setProperty('--hotspot-left', '0%');
          h.classList.remove('visible');
        }
      });
    }

    if (img.complete) apply();
    else img.addEventListener('load', apply, { once: true });

    window.addEventListener('resize', apply);
  }

  function placeBoxUnderHotspot(hotEl, boxEl) {
    if (!hotEl || !boxEl) return;
    const wrapper = hotEl.closest('.image-wrapper');
    if (!wrapper) return;

    const wrapRect = wrapper.getBoundingClientRect();
    const hotRect = hotEl.getBoundingClientRect();
    const hotCenterX = hotRect.left + hotRect.width / 2;
    const topPx = Math.round(hotRect.bottom - wrapRect.top + 8);
    const leftPx = Math.round(hotCenterX - wrapRect.left);

    boxEl.style.left = leftPx + 'px';
    boxEl.style.top = topPx + 'px';
    boxEl.classList.add('show');
    boxEl.hidden = false;
  }

  function closeAllBoxes(wrapper) {
    wrapper.querySelectorAll('.info-box').forEach(b => {
      b.classList.remove('show');
      b.hidden = true;
      b.style.left = '';
      b.style.top = '';
    });
    wrapper.querySelectorAll('.hotspot').forEach(h => h.setAttribute('aria-expanded', 'false'));
  }

  function initWrapper(wrapper) {
    updateHotspotsForWrapper(wrapper);
    wrapper.querySelectorAll('.hotspot').forEach((h, i) => {
      if (!h.hasAttribute('tabindex')) h.setAttribute('tabindex', '0');
      if (!h.dataset.target) h.dataset.target = 'hotspot-target-' + i;
      h.setAttribute('role', 'button');
      h.setAttribute('aria-expanded', 'false');
    });
  }

  document.querySelectorAll('.image-wrapper').forEach(initWrapper);

  /* --- Hotspot click/keyboard --- */
  document.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (hotspot) {
      const wrapper = hotspot.closest('.image-wrapper');
      closeAllBoxes(wrapper);
      const targetId = hotspot.dataset.target;
      const box = wrapper.querySelector('#' + CSS.escape(targetId));
      if (box) {
        placeBoxUnderHotspot(hotspot, box);
        hotspot.setAttribute('aria-expanded', 'true');
      }
    }
  });

  document.addEventListener('click', (e) => {
    const wrapper = e.target.closest('.image-wrapper');
    if (!wrapper) {
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
      return;
    }
    const isHotspot = !!e.target.closest('.hotspot');
    const isBox = !!e.target.closest('.info-box');
    if (!isHotspot && !isBox) closeAllBoxes(wrapper);
  }, true);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      const active = document.activeElement;
      const hotspot = active && active.closest ? active.closest('.hotspot') : null;
      if (hotspot) {
        e.preventDefault();
        hotspot.click();
      }
    } else if (e.key === 'Escape') {
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
    }
  });

  /* --- Mini-map population & sync --- */
  function populateAndInitMiniMap() {
    const panorama = document.querySelector('.panorama');
    const mini = document.querySelector('.mini-map');
    if (!panorama || !mini) return;

    let track = mini.querySelector('.mini-track');
    if (!track) {
      track = document.createElement('div');
      track.className = 'mini-track';
      mini.appendChild(track);
    }

    let highlight = track.querySelector('.mini-highlight');
    if (!highlight) {
      highlight = document.createElement('div');
      highlight.className = 'mini-highlight';
      track.appendChild(highlight);
    }

    Array.from(track.querySelectorAll('img.mini-thumb')).forEach(n => n.remove());

    const wrappers = Array.from(panorama.querySelectorAll('.image-wrapper'));
    const thumbWidth = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--mini-thumb-width')) || 140;

    wrappers.forEach((wrap, idx) => {
      const img = wrap.querySelector('img');
      if (!img) return;
      const thumb = document.createElement('img');
      thumb.className = 'mini-thumb';
      thumb.src = img.src;
      thumb.alt = 'thumb ' + (idx + 1);
      thumb.dataset.index = idx;
      thumb.style.flex = `0 0 ${thumbWidth}px`;
      thumb.addEventListener('click', () => {
        const targetRect = wrap.getBoundingClientRect();
        const panRect = panorama.getBoundingClientRect();
        const offset = targetRect.left - panRect.left + panorama.scrollLeft;
        panorama.scrollTo({ left: Math.max(0, offset), behavior: 'smooth' });
      });
      track.appendChild(thumb);
    });

   function updateHighlight() {
  const wrappers = Array.from(panorama.querySelectorAll('.image-wrapper'));
  const midX = window.innerWidth / 2;

  let active = null;
  wrappers.forEach(w => {
    const r = w.getBoundingClientRect();
    if (r.left <= midX && r.right >= midX) {
      active = w;
    }
  });

  if (active) {
    const index = wrappers.indexOf(active);
    const thumb = track.querySelector(`.mini-thumb[data-index="${index}"]`);
    
    if (thumb) {
      const r = thumb.getBoundingClientRect();
      const t = track.getBoundingClientRect();

      highlight.style.left = (r.left - t.left) + "px";
      highlight.style.top = (r.top - t.top) + "px";
      highlight.style.width = r.width + "px";
      highlight.style.height = r.height + "px";
    }
  }
}


    panorama.addEventListener('scroll', updateHighlight, { passive: true });
    window.addEventListener('resize', updateHighlight);
    setTimeout(updateHighlight, 150);
  }

  populateAndInitMiniMap();
});

