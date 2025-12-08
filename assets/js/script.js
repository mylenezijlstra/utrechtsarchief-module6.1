// assets/js/script.js
// Volledige, bijgewerkte client‑script voor admin: drag, touch, add, update, delete, en positionering van info-box onder hotspot.

const TYPE_LABELS = { desc: 'Beschrijving', remark: 'Opmerking', extra: 'Extra info' };

/* ---------------- helpers ---------------- */
function showStatus(wrapper, message, color = 'black', timeout = 3000) {
  if (!wrapper) return;
  const status = wrapper.querySelector('.save-status');
  if (!status) return;
  status.textContent = message;
  status.style.color = color;
  if (timeout > 0) {
    clearTimeout(status._clearTimer);
    status._clearTimer = setTimeout(() => { status.textContent = ''; }, timeout);
  }
}
function q(wrapper, sel) { return wrapper ? wrapper.querySelector(sel) : null; }
async function apiCall(url, payload) {
  try {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    return await res.json().catch(() => ({ success: false, error: 'Invalid JSON response' }));
  } catch (err) {
    return { success: false, error: err.message || 'Network error' };
  }
}

/* ---------------- helper: lees huidige px-positie van een hotspot ---------------- */
function getHotspotPxPos(hot) {
  if (!hot) return { top: null, left: null };
  // inline style heeft prioriteit (drag), anders data-attributes (server-saved)
  const topRaw = hot.style.top || hot.getAttribute('data-pos-top') || '';
  const leftRaw = hot.style.left || hot.getAttribute('data-pos-left') || '';
  const top = topRaw ? Math.round(parseFloat(String(topRaw).replace('px',''))) : null;
  const left = leftRaw ? Math.round(parseFloat(String(leftRaw).replace('px',''))) : null;
  return { top, left };
}

/* ---------------- drag & touch utility ---------------- */
function makeDraggable(hot, wrapper, onSavePosition) {
  let startX, startY, dragging = false;

  const onMouseDown = (e) => {
    e.preventDefault();
    startX = e.clientX; startY = e.clientY; dragging = false;
    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', onMouseUp);
  };
  const onMouseMove = (e) => {
    if (Math.abs(e.clientX - startX) > 3 || Math.abs(e.clientY - startY) > 3) dragging = true;
    if (dragging) {
      const rect = wrapper.getBoundingClientRect();
      hot.style.top = (e.clientY - rect.top) + 'px';
      hot.style.left = (e.clientX - rect.left) + 'px';
    }
  };
  const onMouseUp = async (e) => {
    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', onMouseUp);
    if (!dragging) return;
    const rect = wrapper.getBoundingClientRect();
    const pos_top = Math.round(e.clientY - rect.top);
    const pos_left = Math.round(e.clientX - rect.left);
    if (typeof onSavePosition === 'function') onSavePosition(pos_top, pos_left);
  };

  const onTouchStart = (e) => {
    const t = e.touches[0]; if (!t) return;
    startX = t.clientX; startY = t.clientY; dragging = false;
    document.addEventListener('touchmove', onTouchMove, { passive: false });
    document.addEventListener('touchend', onTouchEnd);
  };
  const onTouchMove = (e) => {
    const t = e.touches[0]; if (!t) return;
    if (Math.abs(t.clientX - startX) > 3 || Math.abs(t.clientY - startY) > 3) dragging = true;
    if (dragging) {
      const rect = wrapper.getBoundingClientRect();
      hot.style.top = (t.clientY - rect.top) + 'px';
      hot.style.left = (t.clientX - rect.left) + 'px';
    }
  };
  const onTouchEnd = (e) => {
    document.removeEventListener('touchmove', onTouchMove);
    document.removeEventListener('touchend', onTouchEnd);
    if (!dragging) return;
    const last = e.changedTouches[0]; if (!last) return;
    const rect = wrapper.getBoundingClientRect();
    const pos_top = Math.round(last.clientY - rect.top);
    const pos_left = Math.round(last.clientX - rect.left);
    if (typeof onSavePosition === 'function') onSavePosition(pos_top, pos_left);
  };

  hot.addEventListener('mousedown', onMouseDown);
  hot.addEventListener('touchstart', onTouchStart, { passive: false });
}

/* ---------------- existing wrapper init ---------------- */
function initWrapper(wrapper) {
  // desc hotspot
  const descHot = wrapper.querySelector('.hotspot-desc');
  if (descHot) {
    makeDraggable(descHot, wrapper, async (top, left) => {
      const payload = { id: Number(wrapper.dataset.id), action: 'update', type: 'desc', desc_top: top, desc_left: left };
      showStatus(wrapper, 'Opslaan positie...', 'black', 0);
      const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot.php', payload);
      if (res.success) showStatus(wrapper, '✔ Positie opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
    });
  }

  // remark hotspot
  const remarkHot = wrapper.querySelector('.hotspot-remark');
  if (remarkHot) {
    makeDraggable(remarkHot, wrapper, async (top, left) => {
      const payload = { id: Number(wrapper.dataset.id), action: 'update', type: 'remark', remark_top: top, remark_left: left };
      showStatus(wrapper, 'Opslaan positie...', 'black', 0);
      const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot.php', payload);
      if (res.success) showStatus(wrapper, '✔ Positie opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
    });
  }

  // existing extra hotspots
  wrapper.querySelectorAll('.hotspot-extra').forEach(hot => {
    const extraId = hot.dataset.extraId || hot.getAttribute('data-extra-id') || null;

    // find matching info box (prefer adjacent)
    let box = hot.nextElementSibling && hot.nextElementSibling.classList.contains('info-extra') ? hot.nextElementSibling : null;
    if (!box) {
      box = Array.from(wrapper.querySelectorAll('.info-extra')).find(b => {
        const btn = b.querySelector('.save-extra') || b.querySelector('.save-new-extra');
        return btn && String(btn.dataset.extraId || btn.getAttribute('data-extra-id')) === String(extraId);
      }) || null;
    }
    if (box && extraId) box.setAttribute('data-extra-id', extraId);

    // draggable + auto-save position
    makeDraggable(hot, wrapper, async (top, left) => {
      if (!extraId) return;
      const payload = { action: 'update', hotspot_id: Number(wrapper.dataset.id), extra_id: Number(extraId), pos_top: top, pos_left: left };
      showStatus(wrapper, 'Opslaan positie...', 'black', 0);
      const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', payload);
      if (res.success) showStatus(wrapper, '✔ Positie opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
    });

    // save button inside box (zorg dat positie meegestuurd wordt)
    if (box) {
      const saveBtn = box.querySelector('.save-extra');
      if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
          const info_nl = box.querySelector('.extra-info-nl')?.value || '';
          const info_en = box.querySelector('.extra-info-en')?.value || '';
          const image = box.querySelector('.extra-image')?.value || '';
          const pos = getHotspotPxPos(hot);
          const payload = { action: 'update', hotspot_id: Number(wrapper.dataset.id), extra_id: Number(extraId), info_nl, info_en, image, pos_top: pos.top, pos_left: pos.left };
          showStatus(wrapper, 'Opslaan extra...', 'black', 0);
          const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', payload);
          if (res.success) showStatus(wrapper, '✔ Extra opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
        });
      }

      const delBtn = box.querySelector('.delete-extra');
      if (delBtn) {
        delBtn.addEventListener('click', async () => {
          if (!confirm('Weet je zeker dat je deze extra wilt verwijderen?')) return;
          const payload = { action: 'delete', hotspot_id: Number(wrapper.dataset.id), extra_id: Number(extraId) };
          showStatus(wrapper, 'Verwijderen...', 'black', 0);
          const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', payload);
          if (res.success) { hot.remove(); box.remove(); showStatus(wrapper, '✔ Verwijderd', 'green'); } else showStatus(wrapper, '❌ Fout bij verwijderen', 'red');
        });
      }
    }
  });

  // save buttons for desc/remark (stuur positie mee)
  wrapper.querySelectorAll('.save-hotspot').forEach(btn => {
    btn.addEventListener('click', async () => {
      const type = btn.dataset.type;
      const payload = { id: Number(wrapper.dataset.id), action: 'update', type };
      if (type === 'desc') {
        payload.description_nl = q(wrapper, '.info-text-nl')?.value || '';
        payload.description_en = q(wrapper, '.info-text-en')?.value || '';
        const hot = wrapper.querySelector('.hotspot-desc');
        const pos = getHotspotPxPos(hot);
        if (pos.top !== null) payload.desc_top = pos.top;
        if (pos.left !== null) payload.desc_left = pos.left;
      } else if (type === 'remark') {
        payload.remark_nl = q(wrapper, '.remark-nl')?.value || '';
        payload.remark_en = q(wrapper, '.remark-en')?.value || '';
        const hot = wrapper.querySelector('.hotspot-remark');
        const pos = getHotspotPxPos(hot);
        if (pos.top !== null) payload.remark_top = pos.top;
        if (pos.left !== null) payload.remark_left = pos.left;
      }
      showStatus(wrapper, 'Opslaan...', 'black', 0);
      const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot.php', payload);
      if (res.success) showStatus(wrapper, '✔ Opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
    });
  });

  // add-extra button
  const addBtn = wrapper.querySelector('.add-extra');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const panel = wrapper.querySelector('.image-panel') || wrapper;
      const rect = panel.getBoundingClientRect();
      const defaultTop = Math.round(rect.height / 2);
      const defaultLeft = Math.round(rect.width / 2);

      // create hotspot + info box (unsaved)
      const hot = document.createElement('div');
      hot.className = 'hotspot hotspot-extra';
      hot.textContent = 'E';
      hot.style.top = defaultTop + 'px';
      hot.style.left = defaultLeft + 'px';

      const box = document.createElement('div');
      box.className = 'info-box info-extra';
      box.style.display = 'block';
      box.innerHTML = `
        <label>Aanvullende info (NL)</label>
        <textarea class="extra-info-nl" rows="3"></textarea>
        <label>Additional info (EN)</label>
        <textarea class="extra-info-en" rows="3"></textarea>
        <label>Extra afbeelding</label>
        <input class="extra-image" type="text" placeholder="bestandsnaam.jpg">
        <div class="controls" style="margin-top:8px">
          <button class="save-new-extra">Opslaan</button>
          <button class="cancel-new-extra">Annuleren</button>
          <span class="save-status"></span>
        </div>
      `;
      panel.appendChild(hot);
      panel.appendChild(box);

      // draggable (positie saved on save)
      makeDraggable(hot, wrapper, () => { /* no auto-save for unsaved extra */ });

      // cancel
      box.querySelector('.cancel-new-extra').addEventListener('click', () => { hot.remove(); box.remove(); });

      // save new
      box.querySelector('.save-new-extra').addEventListener('click', async () => {
        const info_nl = box.querySelector('.extra-info-nl').value || '';
        const info_en = box.querySelector('.extra-info-en').value || '';
        const image = box.querySelector('.extra-image').value || '';
        const pos = getHotspotPxPos(hot);
        const pos_top = pos.top ?? defaultTop;
        const pos_left = pos.left ?? defaultLeft;
        const payload = { action: 'add', hotspot_id: Number(wrapper.dataset.id), pos_top, pos_left, info_nl, info_en, image };
        showStatus(wrapper, 'Opslaan extra...', 'black', 0);
        const res = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', payload);
        if (res.success && res.insert_id) {
          hot.dataset.extraId = res.insert_id;
          box.setAttribute('data-extra-id', res.insert_id);
          // convert buttons: replace save-new with save-extra and delete
          const saveBtn = document.createElement('button');
          saveBtn.className = 'save-extra';
          saveBtn.dataset.extraId = res.insert_id;
          saveBtn.textContent = 'Opslaan';
          const delBtn = document.createElement('button');
          delBtn.className = 'delete-extra';
          delBtn.dataset.extraId = res.insert_id;
          delBtn.textContent = 'Verwijderen';
          const controls = box.querySelector('.controls');
          controls.innerHTML = '';
          controls.appendChild(saveBtn);
          controls.appendChild(delBtn);
          const statusSpan = document.createElement('span');
          statusSpan.className = 'save-status';
          controls.appendChild(statusSpan);

          // attach handlers
          saveBtn.addEventListener('click', async () => {
            const info_nl2 = box.querySelector('.extra-info-nl').value || '';
            const info_en2 = box.querySelector('.extra-info-en').value || '';
            const image2 = box.querySelector('.extra-image').value || '';
            const pos2 = getHotspotPxPos(hot);
            const payload2 = { action: 'update', hotspot_id: Number(wrapper.dataset.id), extra_id: Number(res.insert_id), info_nl: info_nl2, info_en: info_en2, image: image2, pos_top: pos2.top, pos_left: pos2.left };
            showStatus(wrapper, 'Opslaan extra...', 'black', 0);
            const r2 = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', payload2);
            if (r2.success) showStatus(wrapper, '✔ Extra opgeslagen', 'green'); else showStatus(wrapper, '❌ Fout', 'red');
          });
          delBtn.addEventListener('click', async () => {
            if (!confirm('Weet je zeker dat je deze extra wilt verwijderen?')) return;
            const r3 = await apiCall('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', { action: 'delete', hotspot_id: Number(wrapper.dataset.id), extra_id: Number(res.insert_id) });
            if (r3.success) { hot.remove(); box.remove(); showStatus(wrapper, '✔ Verwijderd', 'green'); } else showStatus(wrapper, '❌ Fout', 'red');
          });
          showStatus(wrapper, '✔ Extra opgeslagen', 'green');
        } else {
          showStatus(wrapper, '❌ Fout bij opslaan', 'red');
        }
      });
    });
  }
}

/* ---------------- toggle info-boxes: positioneer box direct onder hotspot ---------------- */
document.addEventListener('click', (e) => {
  const hot = e.target.closest('.hotspot');
  if (!hot) return;
  const wrapper = hot.closest('.image-wrapper');
  if (!wrapper) return;

  // helper: zet box precies onder hotspot (px-waarden)
  function placeBoxUnderHotspot(hotEl, boxEl) {
    if (!hotEl || !boxEl) return;
    const wrapRect = wrapper.getBoundingClientRect();
    const hotRect = hotEl.getBoundingClientRect();
    const hotCenterX = hotRect.left + hotRect.width / 2;
    const topPx = Math.round(hotRect.bottom - wrapRect.top + 6); // 6px marge
    const leftPx = Math.round(hotCenterX - wrapRect.left);
    boxEl.style.left = leftPx + 'px';
    boxEl.style.top = topPx + 'px';
    boxEl.style.display = 'block';
  }

  // sluit alle andere boxes in deze wrapper
  wrapper.querySelectorAll('.info-box').forEach(b => b.style.display = 'none');

  if (hot.classList.contains('hotspot-extra')) {
    const extraId = hot.dataset.extraId || hot.getAttribute('data-extra-id');
    let box = null;
    if (extraId) box = wrapper.querySelector(`.info-extra[data-extra-id="${extraId}"]`);
    if (!box) box = hot.nextElementSibling && hot.nextElementSibling.classList.contains('info-extra') ? hot.nextElementSibling : wrapper.querySelector('.info-extra');
    if (box) {
      if (box.style.display === 'block') box.style.display = 'none';
      else placeBoxUnderHotspot(hot, box);
    }
    return;
  }

  if (hot.classList.contains('hotspot-desc')) {
    const box = wrapper.querySelector('.info-desc');
    if (!box) return;
    if (box.style.display === 'block') box.style.display = 'none';
    else placeBoxUnderHotspot(hot, box);
  } else if (hot.classList.contains('hotspot-remark')) {
    const box = wrapper.querySelector('.info-remark');
    if (!box) return;
    if (box.style.display === 'block') box.style.display = 'none';
    else placeBoxUnderHotspot(hot, box);
  }
});

/* ---------------- init on DOM ready ---------------- */
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.image-wrapper').forEach(initWrapper);
});
