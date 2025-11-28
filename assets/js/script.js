// Hotspot klik → toggle bijbehorende info-box
document.querySelectorAll('.hotspot').forEach(hotspot => {
    hotspot.addEventListener('click', e => {
      const wrapper = hotspot.closest('.image-wrapper');
      const box = wrapper.querySelector('.info-box'); // zoek naast hotspot
      if (box) {
        box.style.display = (box.style.display === "block") ? "none" : "block";
      }
    });
  
    // Drag functionaliteit
    let isDragging = false;
    hotspot.addEventListener('mousedown', e => {
      e.preventDefault();
      isDragging = false;
      const wrapper = hotspot.closest('.image-wrapper');
      const id = wrapper.dataset.id;
  
      const startX = e.clientX;
      const startY = e.clientY;
  
      const onMouseMove = moveEvent => {
        if (Math.abs(moveEvent.clientX - startX) > 3 || Math.abs(moveEvent.clientY - startY) > 3) {
          isDragging = true;
        }
        if (isDragging) {
          const rect = wrapper.getBoundingClientRect();
          hotspot.style.top = (moveEvent.clientY - rect.top) + "px";
          hotspot.style.left = (moveEvent.clientX - rect.left) + "px";
        }
      };
  
      const onMouseUp = upEvent => {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', onMouseUp);
  
        if (isDragging) {
          const rect = wrapper.getBoundingClientRect();
          const pos_top = upEvent.clientY - rect.top;
          const pos_left = upEvent.clientX - rect.left;
  
          fetch('/utrechtsarchief-module6.1/admin/save_hotspot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, pos_top, pos_left })
          }).then(res => res.json()).then(result => {
            const status = wrapper.querySelector('.save-status');
            if (result.success) {
              status.textContent = "✔ Positie opgeslagen";
              status.style.color = "green";
            } else {
              status.textContent = "❌ Fout bij opslaan";
              status.style.color = "red";
            }
          });
        }
      };
  
      document.addEventListener('mousemove', onMouseMove);
      document.addEventListener('mouseup', onMouseUp);
    });
  });
  
  // Beschrijving opslaan
  document.querySelectorAll('.save-desc').forEach(button => {
    button.addEventListener('click', async e => {
      const wrapper = e.target.closest('.image-wrapper');
      const hotspot = wrapper.querySelector('.hotspot');
      const id = wrapper.dataset.id;
      const text = wrapper.querySelector('.info-text').value;
  
      const response = await fetch('/utrechtsarchief-module6.1/admin/save_description.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id, text })
      });
      const result = await response.json();
  
      const status = wrapper.querySelector('.save-status');
      if (result.success) {
        status.textContent = "✔ Beschrijving opgeslagen";
        status.style.color = "green";
  
        // Sluit popup na opslaan
        wrapper.querySelector('.info-box').style.display = "none";
      } else {
        status.textContent = "❌ Fout bij opslaan";
        status.style.color = "red";
      }
    });
  });
  