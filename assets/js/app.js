// Teksten in beide talen
const translations = {
    nl: {
      title: "PANORAMA VAN UTRECHT",
      subtitle: "Hoe werkt het?",
      icon1: "Zoekbalk gebruiken om plaatsen te vinden",
      icon2: "Klik op de hotspots",
      icon3: "Sleep om te verplaatsen",
      start: "Start Panorama",
      details: "Bekijk Details"
    },
    en: {
      title: "PANORAMA OF UTRECHT",
      subtitle: "How does it work?",
      icon1: "Use the search bar to find places",
      icon2: "Click on the hotspots",
      icon3: "Drag to move around",
      start: "Start Panorama",
      details: "View Details"
    }
  };
  
  let currentLang = localStorage.getItem("lang") || "nl";
  
  // Functie om taal in te stellen
  function setLanguage(lang) {
    document.querySelector(".title").textContent = translations[lang].title;
    document.querySelector(".icon-heading").textContent = translations[lang].subtitle;
    document.querySelectorAll(".icon-item p")[0].textContent = translations[lang].icon1;
    document.querySelectorAll(".icon-item p")[1].textContent = translations[lang].icon2;
    document.querySelectorAll(".icon-item p")[2].textContent = translations[lang].icon3;
    document.querySelectorAll(".button")[0].textContent = translations[lang].start;
    document.querySelectorAll(".button")[1].textContent = translations[lang].details;
  
    // Zet ook de lang-attribuut van <html>
    document.documentElement.lang = lang;
  }
  
  // Event listeners voor de taal-knoppen
  document.getElementById("btn-nl").addEventListener("click", function() {
    currentLang = "nl";
    localStorage.setItem("lang", currentLang);
    setLanguage(currentLang);
  });
  
  document.getElementById("btn-en").addEventListener("click", function() {
    currentLang = "en";
    localStorage.setItem("lang", currentLang);
    setLanguage(currentLang);
  });
  
  // Zet taal bij laden (uit localStorage)
  setLanguage(currentLang);
  
  // Functies voor knoppen
  function startPanorama() {
    console.log("Panorama gestart");
    window.location.href = "panorama.php";
  }
  
  function toonDetails() {
    console.log("Details geopend");
    alert("Hier komen hotspots en extra uitleg (kan ook eigen pagina worden).");
  }
  