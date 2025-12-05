// assets/js/panorama.js
document.addEventListener('DOMContentLoaded', () => {

  // Bereken en zet CSS-variabelen voor hotspots binnen een wrapper
  function updateHotspotsForWrapper(wrapper) {
    const img = wrapper.querySelector('img');
    if (!img) return;

    function apply() {
      const rect = img.getBoundingClientRect();
      const imgWidth = rect.width;
      const imgHeight = rect.height;
      wrapper.querySelectorAll('.hotspot').forEach(h => {
        // probeer dataset px-waarden
        const rawTop = h.dataset.posTop !== undefined ? parseFloat(h.dataset.posTop) : NaN;
        const rawLeft = h.dataset.posLeft !== undefined ? parseFloat(h.dataset.posLeft) : NaN;

        if (!isNaN(rawTop) && !isNaN(rawLeft) && imgWidth > 0 && imgHeight > 0) {
          const topPct = (rawTop / imgHeight) * 100;
          const leftPct = (rawLeft / imgWidth) * 100;
          h.style.setProperty('--hotspot-top', topPct + '%');
          h.style.setProperty('--hotspot-left', leftPct + '%');
          h.style.display = 'flex';
          return;
        }

        // fallback: als inline style al percentages bevat, behoud die
        const inline = h.getAttribute('style') || '';
        const matchTop = inline.match(/--hotspot-top\s*:\s*([0-9.]+%)/);
        const matchLeft = inline.match(/--hotspot-left\s*:\s*([0-9.]+%)/);
        if (matchTop && matchLeft) {
          h.style.setProperty('--hotspot-top', matchTop[1]);
          h.style.setProperty('--hotspot-left', matchLeft[1]);
          h.style.display = 'flex';
          return;
        }

        // geen data: zet op 0% (of verberg)
        h.style.setProperty('--hotspot-top', '0%');
        h.style.setProperty('--hotspot-left', '0%');
      });
    }

    if (img.complete) apply();
    else img.addEventListener('load', apply, { once: true });

    // herbereken bij resize
    window.addEventListener('resize', apply);
  }

  // initialiseer voor elke wrapper
  document.querySelectorAll('.image-wrapper').forEach(wrapper => updateHotspotsForWrapper(wrapper));

  // maak hotspots focusable en aria
  document.querySelectorAll('.hotspot').forEach((h,i) => {
    if (!h.hasAttribute('tabindex')) h.setAttribute('tabindex','0');
    if (!h.dataset.target) h.dataset.target = 'hotspot-target-' + i;
    h.setAttribute('role','button');
    h.setAttribute('aria-expanded','false');
  });

  // klik/toggle handler
  document.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (!hotspot) return;
    const wrapper = hotspot.closest('.image-wrapper');
    if (!wrapper) return;
    const targetId = hotspot.dataset.target;
    if (!targetId) return;
    const box = wrapper.querySelector('#' + CSS.escape(targetId));
    if (!box) return;

    wrapper.querySelectorAll('.info-box').forEach(b => { b.setAttribute('hidden',''); b.classList.remove('show'); });
    if (box.hasAttribute('hidden')) {
      box.removeAttribute('hidden'); box.classList.add('show'); hotspot.setAttribute('aria-expanded','true');
    } else {
      box.setAttribute('hidden',''); box.classList.remove('show'); hotspot.setAttribute('aria-expanded','false');
    }
  });

  // keyboard support
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const active = document.activeElement;
    if (!active) return;
    const hotspot = active.closest ? active.closest('.hotspot') : null;
    if (!hotspot) return;
    e.preventDefault();
    hotspot.click();
  });

  console.log('panorama.js initialized. Hotspots:', document.querySelectorAll('.hotspot').length);
});
