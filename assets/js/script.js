// assets/js/script.js
// Admin client-script: drag, touch, add, update, delete, file-upload voor extra hotspots

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

async function apiForm(url, formData) {
  try {
    const res = await fetch(url, { method: 'POST', body: formData });
    return await res.json().catch(() => ({ success: false, error: 'Invalid JSON response' }));
  } catch (err) {
    return { success: false, error: err.message || 'Network error' };
  }
}

/* ---------------- helper: lees huidige px-positie van een hotspot ---------------- */
function getHotspotPxPos(hot) {
  if (!hot) return { top: null, left: null };
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

/* ---------------- init wrapper ---------------- */
function initWrapper(wrapper) {
  // beschrijving en opmerking hotspots (JSON endpoints)
  ['desc','remark'].forEach(type => {
    const hot = wrapper.querySelector(`.hotspot-${type}`);
    if (hot) {
      makeDraggable(hot, wrapper, async (top,left) => {
        const payload = { id:Number(wrapper.dataset.id), action:'update', type };
        payload[`${type}_top`] = top;
        payload[`${type}_left`] = left;
        showStatus(wrapper,'Opslaan positie...','black',0);
        const res = await apiCall('/utrechtsarchief/admin/save_hotspot.php', payload);
        showStatus(wrapper,res.success?'✔ Positie opgeslagen':'❌ Fout',res.success?'green':'red');
      });
    }
  });

  // bestaande extra hotspots (FormData endpoints)
  wrapper.querySelectorAll('.hotspot-extra').forEach(hot => {
    const extraId = hot.dataset.extraId || hot.getAttribute('data-extra-id') || null;

    // koppel juiste info-box
    let box = hot.nextElementSibling && hot.nextElementSibling.classList.contains('info-extra') ? hot.nextElementSibling : null;
    if (!box) {
      box = Array.from(wrapper.querySelectorAll('.info-extra')).find(b => {
        const btn = b.querySelector('.save-extra') || b.querySelector('.save-new-extra');
        return btn && String(btn.dataset.extraId || btn.getAttribute('data-extra-id')) === String(extraId);
      }) || null;
    }
    if (box && extraId) box.setAttribute('data-extra-id', extraId);

    // drag + positie opslaan
    makeDraggable(hot, wrapper, async (top,left) => {
      if (!extraId) return;
      const formData = new FormData();
      formData.append('action','update');
      formData.append('hotspot_id', wrapper.dataset.id);
      formData.append('extra_id', extraId);
      formData.append('pos_top', top);
      formData.append('pos_left', left);
      showStatus(wrapper,'Opslaan positie...','black',0);
      const res = await apiForm('/utrechtsarchief/admin/save_hotspot_extra.php', formData);
      showStatus(wrapper,res.success?'✔ Positie opgeslagen':'❌ Fout',res.success?'green':'red');
    });

    // save/update knoppen in de box
    if (box) {
      const saveBtn = box.querySelector('.save-extra');
      if (saveBtn) {
        saveBtn.addEventListener('click', async () => {
          const info_nl = box.querySelector('.extra-info-nl')?.value || '';
          const info_en = box.querySelector('.extra-info-en')?.value || '';
          const fileInput = box.querySelector('.extra-image');
          const pos = getHotspotPxPos(hot);

          const formData = new FormData();
          formData.append('action','update');
          formData.append('hotspot_id', wrapper.dataset.id);
          formData.append('extra_id', extraId);
          formData.append('info_nl', info_nl);
          formData.append('info_en', info_en);
          formData.append('pos_top', pos.top);
          formData.append('pos_left', pos.left);
          if (fileInput && fileInput.files.length > 0) {
            formData.append('image', fileInput.files[0]);
          }

          showStatus(wrapper,'Opslaan extra...','black',0);
          const res = await apiForm('/utrechtsarchief/admin/save_hotspot_extra.php', formData);
          showStatus(wrapper,res.success?'✔ Extra opgeslagen':'❌ Fout',res.success?'green':'red');
        });
      }

      const delBtn = box.querySelector('.delete-extra');
      if (delBtn) {
        delBtn.addEventListener('click', async () => {
          if (!confirm('Weet je zeker dat je deze extra wilt verwijderen?')) return;
          const formData = new FormData();
          formData.append('action','delete');
          formData.append('hotspot_id', wrapper.dataset.id);
          formData.append('extra_id', extraId);
          showStatus(wrapper,'Verwijderen...','black',0);
          const res = await apiForm('/utrechtsarchief/admin/save_hotspot_extra.php', formData);
          if (res.success) { hot.remove(); box.remove(); showStatus(wrapper,'✔ Verwijderd','green'); }
          else showStatus(wrapper,'❌ Fout bij verwijderen','red');
        });
      }
    }
  });

  // save-knoppen voor beschrijving/remark (JSON)
  wrapper.querySelectorAll('.save-hotspot').forEach(btn => {
    btn.addEventListener('click', async () => {
      const type = btn.dataset.type;
      const payload = { id: Number(wrapper.dataset.id), action: 'update', type };
      if (type === 'desc') {
        payload.description_nl = q(wrapper, '.info-text-nl')?.value || '';
        payload.description_en = q(wrapper, '.info-text-en')?.value || '';
        const pos = getHotspotPxPos(wrapper.querySelector('.hotspot-desc'));
        if (pos.top !== null) payload.desc_top = pos.top;
        if (pos.left !== null) payload.desc_left = pos.left;
      } else if (type === 'remark') {
        payload.remark_nl = q(wrapper, '.remark-nl')?.value || '';
        payload.remark_en = q(wrapper, '.remark-en')?.value || '';
        const pos = getHotspotPxPos(wrapper.querySelector('.hotspot-remark'));
        if (pos.top !== null) payload.remark_top = pos.top;
        if (pos.left !== null) payload.remark_left = pos.left;
      }
      showStatus(wrapper,'Opslaan...','black',0);
      const res = await apiCall('/utrechtsarchief/admin/save_hotspot.php', payload);
      showStatus(wrapper,res.success?'✔ Opgeslagen':'❌ Fout',res.success?'green':'red');
    });
  });

  // add-extra knop (FormData voor nieuwe extra + file upload)
  const addBtn = wrapper.querySelector('.add-extra');
  if (addBtn) {
    addBtn.addEventListener('click', () => {
      const panel = wrapper.querySelector('.image-panel') || wrapper;
      const rect = panel.getBoundingClientRect();
      const defaultTop = Math.round(rect.height / 2);
      const defaultLeft = Math.round(rect.width / 2);

      // nieuwe hotspot + box
      const hot = document.createElement('div');
      hot.className = 'hotspot hotspot-extra';
      hot.textContent = 'i';
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
        <input class="extra-image" type="file" accept="image/*">
        <div class="controls" style="margin-top:8px">
          <button class="save-new-extra">Opslaan</button>
          <button class="cancel-new-extra">Annuleren</button>
          <span class="save-status"></span>
        </div>
      `;
      panel.appendChild(hot);
      panel.appendChild(box);

      makeDraggable(hot, wrapper, () => { /* geen autosave voor nieuwe extra */ });

      // annuleren
      box.querySelector('.cancel-new-extra').addEventListener('click', () => { hot.remove(); box.remove(); });

      // opslaan nieuwe extra
      box.querySelector('.save-new-extra').addEventListener('click', async () => {
        const info_nl = box.querySelector('.extra-info-nl').value || '';
        const info_en = box.querySelector('.extra-info-en').value || '';
        const fileInput = box.querySelector('.extra-image');
        const pos = getHotspotPxPos(hot);
        const pos_top = pos.top ?? defaultTop;
        const pos_left = pos.left ?? defaultLeft;

        const formData = new FormData();
        formData.append('action','add');
        formData.append('hotspot_id', wrapper.dataset.id);
        formData.append('pos_top', pos_top);
        formData.append('pos_left', pos_left);
        formData.append('info_nl', info_nl);
        formData.append('info_en', info_en);
        if (fileInput && fileInput.files.length > 0) {
          formData.append('image', fileInput.files[0]);
        }

        showStatus(wrapper,'Opslaan extra...','black',0);
        const res = await apiForm('/utrechtsarchief/admin/save_hotspot_extra.php', formData);
        if (res.success && res.insert_id) {
          // set id en knoppen omzetten
          hot.dataset.extraId = res.insert_id;
          box.setAttribute('data-extra-id', res.insert_id);
          const controls = box.querySelector('.controls');
          controls.innerHTML = '';
          const saveBtn = document.createElement('button');
          saveBtn.className = 'save-extra';
          saveBtn.dataset.extraId = res.insert_id;
          saveBtn.textContent = 'Opslaan';
          const delBtn = document.createElement('button');
          delBtn.className = 'delete-extra';
          delBtn.dataset.extraId = res.insert_id;
          delBtn.textContent = 'Verwijderen';
          const statusSpan = document.createElement('span');
          statusSpan.className = 'save-status';
          controls.appendChild(saveBtn);
          controls.appendChild(delBtn);
          controls.appendChild(statusSpan);

          // handlers voor nieuwe knoppen
          saveBtn.addEventListener('click', async () => {
            const info_nl2 = box.querySelector('.extra-info-nl').value || '';
            const info_en2 = box.querySelector('.extra-info-en').value || '';
            const fileInput2 = box.querySelector('.extra-image');
            const pos2 = getHotspotPxPos(hot);
            const formData2 = new FormData();
            formData2.append('action','update');
            formData2.append('hotspot_id', wrapper.dataset.id);
            formData2.append('extra_id', res.insert_id);
            formData2.append('info_nl', info_nl2);
            formData2.append('info_en', info_en2);
            formData2.append('pos_top', pos2.top);
            formData2.append('pos_left', pos2.left);
            if (fileInput2 && fileInput2.files.length > 0) {
              formData2.append('image', fileInput2.files[0]);
            }
            showStatus(wrapper,'Opslaan extra...','black',0);
            const r2 = await apiForm('/utrechtsarchief/admin/save_hotspot_extra.php', formData2);
            showStatus(wrapper, r2.success ? '✔ Extra opgeslagen' : '❌ Fout', r2.success ? 'green' : 'red');
          });

          delBtn.addEventListener('click', async () => {
            if (!confirm('Weet je zeker dat je deze extra wilt verwijderen?')) return;
            const r3Form = new FormData();
            r3Form.append('action','delete');
            r3Form.append('hotspot_id', wrapper.dataset.id);
            r3Form.append('extra_id', res.insert_id);
            const r3 = await apiForm('/utrechtsarchief-module6.1/admin/save_hotspot_extra.php', r3Form);
            if (r3.success) { hot.remove(); box.remove(); showStatus(wrapper,'✔ Verwijderd','green'); }
            else showStatus(wrapper,'❌ Fout','red');
          });

          showStatus(wrapper,'✔ Extra opgeslagen','green');
        } else {
          showStatus(wrapper,'❌ Fout bij opslaan','red');
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

  function placeBoxUnderHotspot(hotEl, boxEl) {
    if (!hotEl || !boxEl) return;
    const wrapRect = wrapper.getBoundingClientRect();
    const hotRect = hotEl.getBoundingClientRect();
    const hotCenterX = hotRect.left + hotRect.width / 2;
    const topPx = Math.round(hotRect.bottom - wrapRect.top + 6);
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


// Sluit info-boxen bij klik buiten
document.addEventListener('click', function (e) {
  // alle info-boxen ophalen
  const boxes = document.querySelectorAll('.info-box');

  boxes.forEach(box => {
    // als de box zichtbaar is en de klik niet in de box zelf of op een hotspot was
    if (box.style.display !== 'none') {
      const clickedInsideBox = box.contains(e.target);
      const clickedHotspot = e.target.classList.contains('hotspot');

      if (!clickedInsideBox && !clickedHotspot) {
        box.style.display = 'none';
      }
    }
  });
});