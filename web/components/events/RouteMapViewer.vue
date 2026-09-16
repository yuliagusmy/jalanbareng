<template>
  <div style="height: 400px; width: 100%; border-radius: 8px; overflow: hidden;">
    <l-map
      ref="map"
      v-model:zoom="zoom"
      :center="center"
      :use-global-leaflet="false"
    >
      <l-tile-layer
        url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
        layer-type="base"
        name="OpenStreetMap"
      ></l-tile-layer>
      <l-polyline :lat-lngs="routePoints" color="blue"></l-polyline>
      <l-marker v-for="(point, i) in routePoints" :key="i" :lat-lng="point"></l-marker>
      <l-marker v-if="showUserLocation && userLocation" :lat-lng="userLocation" :icon="userIcon"></l-marker>
    </l-map>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, onUnmounted } from 'vue'
import "leaflet/dist/leaflet.css";
import { LMap, LTileLayer, LPolyline, LMarker } from "@vue-leaflet/vue-leaflet";
import { latLng, Icon } from 'leaflet';
import type { LatLngExpression } from 'leaflet';

// Fix for default icon issue
import markerIcon2x from 'leaflet/dist/images/marker-icon-2x.png';
import markerIcon from 'leaflet/dist/images/marker-icon.png';
import markerShadow from 'leaflet/dist/images/marker-shadow.png';

// Custom icon for user location
const userIcon = new Icon({
  iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
  shadowUrl: markerShadow,
  iconSize: [25, 41],
  iconAnchor: [12, 41],
  popupAnchor: [1, -34],
  shadowSize: [41, 41]
});

type D = Icon.Default & { _getIconUrl?: string; };
delete (Icon.Default.prototype as D)._getIconUrl;
Icon.Default.mergeOptions({ iconRetinaUrl: markerIcon2x, iconUrl: markerIcon, shadowUrl: markerShadow });

const props = defineProps({
  route: {
    type: Array as () => Array<{ lat: number, lng: number }>,
    default: () => []
  },
  showUserLocation: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:modelValue', 'update:distance'])

const map = ref(null)
const zoom = ref(13)
const center = ref<LatLngExpression>([-5.1477, 119.4327]) // Default to Makassar
const userLocation = ref<LatLngExpression | null>(null)
let watchId: number | null = null;

const routePoints = ref<LatLngExpression[]>(props.route ? props.route.map(p => [p.lat, p.lng]) : [])

const distance = computed(() => {
  if (routePoints.value.length < 2) return 0
  let total = 0
  for (let i = 0; i < routePoints.value.length - 1; i++) {
    const from = latLng(routePoints.value[i])
    const to = latLng(routePoints.value[i+1])
    total += from.distanceTo(to)
  }
  return total / 1000 // km
})

watch(distance, (newDistance) => { emit('update:distance', newDistance) })

watch(() => props.route, (newRoute) => {
  routePoints.value = newRoute ? newRoute.map(p => [p.lat, p.lng]) : [];
  if (routePoints.value.length > 0) {
    center.value = routePoints.value[0];
  }
});

onMounted(() => {
  if (props.route && props.route.length > 0) {
    center.value = [props.route[0].lat, props.route[0].lng];
  }
  // Invalidate map size after a short delay to ensure it renders correctly
  setTimeout(() => {
    if (map.value) {
      map.value.leafletObject.invalidateSize();
    }
  }, 200);

  if (props.showUserLocation && navigator.geolocation) {
    watchId = navigator.geolocation.watchPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        userLocation.value = [latitude, longitude];
        // Optionally center map on user's location if it's the first update or map is not centered on route
        if (routePoints.value.length === 0) {
          center.value = userLocation.value;
        }
      },
      (error) => {
        console.error('Error getting user location:', error);
      },
      { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
    );
  }
});

onUnmounted(() => {
  if (watchId !== null) {
    navigator.geolocation.clearWatch(watchId);
  }
});
</script>
