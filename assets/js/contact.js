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
      html: '<i class="fa-solid fa-location-dot contact-page__map-pin" aria-hidden="true"></i>',
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
