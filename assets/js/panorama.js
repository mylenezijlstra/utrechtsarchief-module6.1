document.querySelectorAll('.hotspot').forEach(hotspot => {
  hotspot.addEventListener('click', () => {
    const wrapper = hotspot.closest('.image-wrapper');
    const infoBox = wrapper.querySelector('.info-box');
    infoBox.style.display = infoBox.style.display === 'block' ? 'none' : 'block';
  });
});
