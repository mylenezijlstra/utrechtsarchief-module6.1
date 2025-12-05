document.querySelectorAll('.hotspot').forEach(hotspot => {
  
  hotspot.addEventListener('click', () => {
    const wrapper = hotspot.closest('.image-wrapper');
    const infoBox = wrapper.querySelector('.info-box');
    if (infoBox) {
      infoBox.classList.toggle('show');
    }
  });
});

const panorama = document.querySelector('.panorama');
const miniHighlight = document.querySelector('.mini-highlight');
const miniMap = document.querySelector('.mini-map');

function updateHighlight() {
  // verhouding van scrollpositie in panorama
  const scrollRatio = panorama.scrollLeft / (panorama.scrollWidth - panorama.clientWidth);

  // basisbreedte van highlight = verhouding zichtbaar deel
  const baseWidth = (panorama.clientWidth / panorama.scrollWidth) * miniMap.clientWidth;

  // maak het vakje breder met factor + marge
  const highlightWidth = baseWidth * 1.3 + 30; // breder dan baseWidth
  miniHighlight.style.width = highlightWidth + 'px';

  // hoogte bijna de hele mini-map
  miniHighlight.style.height = (miniMap.clientHeight * 0.9) + 'px';

  // maximale verplaatsing van highlight
  const highlightMax = miniMap.clientWidth - highlightWidth;

  // positie berekenen met scrollRatio en highlightMax
  miniHighlight.style.left = (scrollRatio * highlightMax) + 'px';
}

// events
panorama.addEventListener('scroll', updateHighlight);
window.addEventListener('resize', updateHighlight);
updateHighlight(); // initiale positie
