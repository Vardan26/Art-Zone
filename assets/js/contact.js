document.addEventListener("DOMContentLoaded", () => {
  const contactMap = document.querySelector(".contact-page__map-canvas");

  if (!contactMap || typeof window.L === "undefined") {
    return;
  }

  try {
    const markers = JSON.parse(contactMap.dataset.mapMarkers || "[]");

    if (!Array.isArray(markers) || !markers.length) {
      return;
    }

    const map = window.L.map(contactMap, {
      scrollWheelZoom: false,
    });

    window.L.tileLayer(
      "https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png",
      {
        subdomains: "abcd",
        maxZoom: 20,
        attribution: "&copy; OpenStreetMap contributors &copy; CARTO",
      },
    ).addTo(map);

    const markerLayer = [];
    const icon = window.L.divIcon({
      html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" fill="currentColor" class="contact-page__map-pin" aria-hidden="true"><path d="M215.7 499.2C267 435 384 279.4 384 192C384 86 298 0 192 0S0 86 0 192c0 87.4 117 243 168.3 307.2c12.3 15.3 35.1 15.3 47.4 0zM192 128a64 64 0 1 1 0 128 64 64 0 1 1 0-128z"/></svg>',
      className: "contact-page__map-pin-wrap",
      iconSize: [24, 24],
      iconAnchor: [12, 24],
      popupAnchor: [0, -18],
    });

    markers.forEach((marker) => {
      if (
        typeof marker.lat !== "number" ||
        typeof marker.lng !== "number" ||
        Number.isNaN(marker.lat) ||
        Number.isNaN(marker.lng)
      ) {
        return;
      }

      const mapMarker = window.L.marker([marker.lat, marker.lng], {
        icon,
      }).addTo(map);
      const popupContent = [marker.label, marker.text]
        .filter(Boolean)
        .join("<br>");

      if (popupContent) {
        mapMarker.bindPopup(popupContent);
      }

      markerLayer.push(mapMarker);
    });

    if (markerLayer.length === 1) {
      map.setView(markerLayer[0].getLatLng(), 11);
      return;
    }

    if (markerLayer.length > 1) {
      const group = window.L.featureGroup(markerLayer);
      map.fitBounds(group.getBounds(), { padding: [40, 40] });
    }
  } catch (error) {
    // Leave the map area empty if marker data is malformed.
  }
});
