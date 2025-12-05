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
    const onResize = () => apply();
    window.addEventListener('resize', onResize);

    // cleanup reference (optioneel) - niet strikt nodig hier, maar netjes
    wrapper._panoramaResizeHandler = onResize;
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

  // Helper: sluit alle info-boxes binnen een wrapper
  function closeAllBoxes(wrapper) {
    wrapper.querySelectorAll('.info-box').forEach(b => {
      b.setAttribute('hidden','');
      b.classList.remove('show');
    });
    // zet aria-expanded false op alle hotspots in wrapper
    wrapper.querySelectorAll('.hotspot').forEach(h => h.setAttribute('aria-expanded','false'));
  }

  // klik/toggle handler (verbeterd zodat tweede klik sluit)
  document.addEventListener('click', (e) => {
    const hotspot = e.target.closest('.hotspot');
    if (!hotspot) return;
    const wrapper = hotspot.closest('.image-wrapper');
    if (!wrapper) return;
    const targetId = hotspot.dataset.target;
    if (!targetId) return;
    const box = wrapper.querySelector('#' + CSS.escape(targetId));
    if (!box) return;

    // bepaal of de doelbox nu zichtbaar is (voordat we andere verbergen)
    const wasVisible = !box.hasAttribute('hidden') && box.classList.contains('show');

    // verberg alle boxes in deze wrapper
    closeAllBoxes(wrapper);

    // toggle: als het eerder niet zichtbaar was, toon het; anders blijft alles gesloten
    if (!wasVisible) {
      box.removeAttribute('hidden');
      box.classList.add('show');
      hotspot.setAttribute('aria-expanded','true');
      // focus op eerste focusbaar element in box voor toegankelijkheid
      const focusable = box.querySelector('button, [tabindex], input, textarea, a');
      if (focusable) focusable.focus();
    } else {
      hotspot.setAttribute('aria-expanded','false');
    }
  });

  // sluit box bij klik buiten (per wrapper)
  document.addEventListener('click', (e) => {
    // als klik niet binnen een image-wrapper plaatsvond, sluit alle open boxes op de pagina
    const wrapper = e.target.closest('.image-wrapper');
    if (!wrapper) {
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
      return;
    }
    // als klik binnen wrapper maar niet op hotspot of info-box, sluit boxes in die wrapper
    const isHotspot = !!e.target.closest('.hotspot');
    const isBox = !!e.target.closest('.info-box');
    if (!isHotspot && !isBox) closeAllBoxes(wrapper);
  }, true);

  // keyboard support: Enter of Space activeert hotspot; Escape sluit open box
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' || e.key === ' ') {
      const active = document.activeElement;
      if (!active) return;
      const hotspot = active.closest ? active.closest('.hotspot') : null;
      if (!hotspot) return;
      e.preventDefault();
      hotspot.click();
    } else if (e.key === 'Escape') {
      // sluit alle open boxes op Escape
      document.querySelectorAll('.image-wrapper').forEach(w => closeAllBoxes(w));
    }
  });

  // optionele console log
  console.log('panorama.js initialized. Hotspots:', document.querySelectorAll('.hotspot').length);
});
