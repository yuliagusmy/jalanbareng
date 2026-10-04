<template>
  <div class="calendar-page">
    <!-- Page Header -->
    <section class="calendar-header">
      <v-container class="py-10 py-md-14">
        <div class="text-center">
          <v-chip size="small" color="primary" variant="flat" class="font-weight-bold mb-4 text-uppercase"
            style="font-size: 0.7rem; letter-spacing: 0.1em;">
            <v-icon start size="14">mdi-calendar-star</v-icon>
            Jadwal Aktivasi
          </v-chip>
          <h1 class="calendar-headline mb-3">Kalender Jalan Bareng</h1>
          <p class="text-body-1 text-grey-darken-1 mx-auto" style="max-width: 540px;">
            Temukan jadwal walking tour, eksplorasi kota, dan event komunitas yang akan datang di kotamu.
          </p>
        </div>
      </v-container>
    </section>

    <v-container class="pb-16">

      <!-- Month Navigator -->
      <div class="month-nav d-flex align-center justify-space-between mb-6">
        <v-btn icon variant="text" @click="prevMonth" aria-label="Bulan sebelumnya">
          <v-icon>mdi-chevron-left</v-icon>
        </v-btn>

        <div class="text-center">
          <div class="month-label">{{ monthLabel }}</div>
          <div class="year-label">{{ currentYear }}</div>
        </div>

        <v-btn icon variant="text" @click="nextMonth" aria-label="Bulan berikutnya">
          <v-icon>mdi-chevron-right</v-icon>
        </v-btn>
      </div>

      <!-- Today Button -->
      <div class="d-flex justify-center mb-6">
        <v-btn
          size="small"
          variant="outlined"
          rounded="pill"
          color="primary"
          @click="goToToday"
          class="font-weight-bold"
        >
          <v-icon start size="16">mdi-calendar-today</v-icon>
          Hari Ini
        </v-btn>
      </div>

      <!-- Calendar Grid -->
      <div class="calendar-grid-wrapper mb-8">
        <!-- Day Labels -->
        <div class="day-labels-row">
          <div v-for="day in dayLabels" :key="day" class="day-label">{{ day }}</div>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="d-flex justify-center py-12">
          <v-progress-circular indeterminate color="primary" size="40" />
        </div>

        <!-- Calendar Days -->
        <div v-else class="days-grid">
          <div
            v-for="cell in calendarCells"
            :key="cell.key"
            class="day-cell"
            :class="{
              'day-empty': !cell.day,
              'day-today': cell.isToday,
              'day-has-events': cell.events.length > 0,
              'day-selected': selectedDay === cell.day && cell.day,
              'day-other-month': cell.otherMonth,
            }"
            @click="cell.day && selectDay(cell)"
          >
            <span v-if="cell.day" class="day-number">{{ cell.day }}</span>

            <!-- Event Dots -->
            <div v-if="cell.events.length > 0" class="event-dots">
              <span
                v-for="(ev, i) in cell.events.slice(0, 3)"
                :key="i"
                class="event-dot"
                :class="getEventTypeClass(ev.type)"
              ></span>
              <span v-if="cell.events.length > 3" class="event-dot event-dot-more">+</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Legend -->
      <div class="d-flex justify-center flex-wrap ga-4 mb-8">
        <div v-for="leg in legend" :key="leg.label" class="d-flex align-center ga-2">
          <span class="legend-dot" :class="leg.class"></span>
          <span class="text-caption text-grey-darken-2">{{ leg.label }}</span>
        </div>
      </div>

      <!-- Selected Day Events -->
      <div v-if="selectedDayEvents.length > 0" class="selected-events">
        <div class="d-flex align-center ga-3 mb-5">
          <div class="events-accent"></div>
          <h2 class="text-h6 font-weight-black">
            Event pada {{ selectedDateLabel }}
          </h2>
          <v-chip size="x-small" color="primary" variant="flat" class="font-weight-bold">
            {{ selectedDayEvents.length }}
          </v-chip>
        </div>

        <v-row>
          <v-col
            v-for="event in selectedDayEvents"
            :key="event.id"
            cols="12"
            sm="6"
            md="4"
          >
            <nuxt-link :to="`/events/${event.id}`" class="event-card text-decoration-none">
              <!-- Event poster / fallback -->
              <div class="event-card-img" :class="getEventTypeClass(event.type)">
                <img
                  v-if="event.poster"
                  :src="getImageUrl(event.poster)"
                  :alt="event.name"
                  class="event-poster"
                />
                <div class="event-card-overlay"></div>
                <div class="event-date-badge">
                  <span class="badge-day">{{ formatDay(event.date) }}</span>
                  <span class="badge-month">{{ formatMonth(event.date) }}</span>
                </div>
                <v-chip size="x-small" class="event-type-chip font-weight-bold text-uppercase" color="white" variant="flat">
                  {{ eventTypeLabel(event.type) }}
                </v-chip>
              </div>

              <div class="event-card-body">
                <h3 class="event-title">{{ event.name }}</h3>
                <div class="d-flex flex-wrap ga-2 mt-2">
                  <div class="d-flex align-center ga-1 text-caption text-grey-darken-1">
                    <v-icon size="13">mdi-clock-outline</v-icon>
                    <span>{{ formatTime(event.date) }}</span>
                  </div>
                  <div v-if="event.start_point" class="d-flex align-center ga-1 text-caption text-grey-darken-1">
                    <v-icon size="13">mdi-map-marker-outline</v-icon>
                    <span class="text-truncate" style="max-width: 140px;">{{ event.start_point }}</span>
                  </div>
                </div>
              </div>
            </nuxt-link>
          </v-col>
        </v-row>
      </div>

      <!-- Upcoming Events (when no day selected) -->
      <div v-else-if="!loading">
        <div class="d-flex align-center ga-3 mb-5">
          <div class="events-accent"></div>
          <h2 class="text-h6 font-weight-black">Event Mendatang — {{ monthLabel }}</h2>
        </div>

        <div v-if="upcomingEvents.length === 0" class="empty-state text-center py-12">
          <v-icon size="64" color="grey-lighten-2">mdi-calendar-blank-outline</v-icon>
          <p class="text-subtitle-2 text-grey-darken-1 mt-3 mb-1">Belum ada event bulan ini</p>
          <p class="text-caption text-grey">Cek bulan berikutnya atau pantau media sosial kami.</p>
        </div>

        <v-row v-else>
          <v-col
            v-for="event in upcomingEvents"
            :key="event.id"
            cols="12"
            sm="6"
            md="4"
          >
            <nuxt-link :to="`/events/${event.id}`" class="event-card text-decoration-none">
              <div class="event-card-img" :class="getEventTypeClass(event.type)">
                <img
                  v-if="event.poster"
                  :src="getImageUrl(event.poster)"
                  :alt="event.name"
                  class="event-poster"
                />
                <div class="event-card-overlay"></div>
                <div class="event-date-badge">
                  <span class="badge-day">{{ formatDay(event.date) }}</span>
                  <span class="badge-month">{{ formatMonth(event.date) }}</span>
                </div>
                <v-chip size="x-small" class="event-type-chip font-weight-bold text-uppercase" color="white" variant="flat">
                  {{ eventTypeLabel(event.type) }}
                </v-chip>
              </div>
              <div class="event-card-body">
                <h3 class="event-title">{{ event.name }}</h3>
                <div class="d-flex flex-wrap ga-2 mt-2">
                  <div class="d-flex align-center ga-1 text-caption text-grey-darken-1">
                    <v-icon size="13">mdi-clock-outline</v-icon>
                    <span>{{ formatTime(event.date) }}</span>
                  </div>
                  <div v-if="event.start_point" class="d-flex align-center ga-1 text-caption text-grey-darken-1">
                    <v-icon size="13">mdi-map-marker-outline</v-icon>
                    <span class="text-truncate" style="max-width: 140px;">{{ event.start_point }}</span>
                  </div>
                </div>
              </div>
            </nuxt-link>
          </v-col>
        </v-row>
      </div>

    </v-container>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'

definePageMeta({ layout: 'default' })

useSeoMeta({
  title: 'Kalender Aktivasi - Jalan Bareng',
  description: 'Jadwal lengkap walking tour, eksplorasi kota, dan event komunitas Jalan Bareng di seluruh Indonesia.',
})

const { api } = useApi()
const { getImageUrl } = useImageUrl()

// ── State ──
const today = new Date()
const currentMonth = ref(today.getMonth())
const currentYear = ref(today.getFullYear())
const loading = ref(false)
const allEvents = ref<any[]>([])
const selectedDay = ref<number | null>(null)
const selectedCell = ref<any>(null)

// ── Labels ──
const dayLabels = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']
const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
  'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

const monthLabel = computed(() => monthNames[currentMonth.value])

const legend = [
  { label: 'Reguler', class: 'dot-regular' },
  { label: 'Spesial', class: 'dot-special' },
  { label: 'Bulanan', class: 'dot-monthly' },
  { label: 'Lainnya', class: 'dot-other' },
]

// ── Navigation ──
const prevMonth = () => {
  if (currentMonth.value === 0) {
    currentMonth.value = 11
    currentYear.value--
  } else {
    currentMonth.value--
  }
  selectedDay.value = null
  fetchEvents()
}

const nextMonth = () => {
  if (currentMonth.value === 11) {
    currentMonth.value = 0
    currentYear.value++
  } else {
    currentMonth.value++
  }
  selectedDay.value = null
  fetchEvents()
}

const goToToday = () => {
  const t = new Date()
  currentMonth.value = t.getMonth()
  currentYear.value = t.getFullYear()
  selectedDay.value = null
  fetchEvents()
}

// ── Fetch Events ──
const fetchEvents = async () => {
  loading.value = true
  try {
    const res = await api.get('/events', {
      params: {
        per_page: 100,
        month: currentMonth.value + 1,
        year: currentYear.value,
        sort: 'asc',
      }
    })
    allEvents.value = res.data?.data || res.data?.events || []
  } catch (err) {
    console.error('Failed to fetch events:', err)
    allEvents.value = []
  } finally {
    loading.value = false
  }
}

// ── Calendar Grid Builder ──
const calendarCells = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const firstDay = new Date(year, month, 1).getDay()
  const daysInMonth = new Date(year, month + 1, 0).getDate()
  const cells: any[] = []

  // Empty leading cells
  for (let i = 0; i < firstDay; i++) {
    cells.push({ key: `empty-${i}`, day: null, events: [], isToday: false, otherMonth: true })
  }

  // Day cells
  for (let d = 1; d <= daysInMonth; d++) {
    const cellDate = new Date(year, month, d)
    const isToday =
      d === today.getDate() &&
      month === today.getMonth() &&
      year === today.getFullYear()

    const events = allEvents.value.filter(ev => {
      if (!ev.date) return false
      const evDate = new Date(ev.date)
      return evDate.getDate() === d &&
        evDate.getMonth() === month &&
        evDate.getFullYear() === year
    })

    cells.push({ key: `day-${d}`, day: d, events, isToday, otherMonth: false })
  }

  return cells
})

// ── Upcoming Events (current month, future dates) ──
const upcomingEvents = computed(() => {
  return allEvents.value
    .filter(ev => ev.date)
    .sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime())
    .slice(0, 9)
})

// ── Selected Day ──
const selectDay = (cell: any) => {
  if (selectedDay.value === cell.day) {
    selectedDay.value = null
    selectedCell.value = null
  } else {
    selectedDay.value = cell.day
    selectedCell.value = cell
  }
}

const selectedDayEvents = computed(() => selectedCell.value?.events || [])

const selectedDateLabel = computed(() => {
  if (!selectedDay.value) return ''
  return `${selectedDay.value} ${monthLabel.value} ${currentYear.value}`
})

// ── Formatters ──
const formatDay = (dateStr: string) => {
  if (!dateStr) return ''
  return new Date(dateStr).getDate().toString().padStart(2, '0')
}

const formatMonth = (dateStr: string) => {
  if (!dateStr) return ''
  return monthNames[new Date(dateStr).getMonth()].substring(0, 3).toUpperCase()
}

const formatTime = (dateStr: string) => {
  if (!dateStr) return ''
  return new Date(dateStr).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false })
}

const getEventTypeClass = (type: string) => {
  switch (type) {
    case 'regular': return 'type-regular'
    case 'special': return 'type-special'
    case 'monthly': return 'type-monthly'
    default: return 'type-other'
  }
}

const eventTypeLabel = (type: string) => {
  switch (type) {
    case 'regular': return 'Reguler'
    case 'special': return 'Spesial'
    case 'monthly': return 'Bulanan'
    default: return 'Event'
  }
}

onMounted(() => {
  fetchEvents()
})
</script>

<style scoped>
/* ─── Page Header ─── */
.calendar-page {
  background: #FAFAF9;
  min-height: 100vh;
}

.calendar-header {
  background: white;
  border-bottom: 1px solid #E5E7EB;
}

.calendar-headline {
  font-size: clamp(1.75rem, 3.5vw, 2.75rem);
  font-weight: 900;
  letter-spacing: -0.035em;
  color: #111827;
  line-height: 1.2;
}

/* ─── Month Navigator ─── */
.month-nav {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 16px;
  padding: 12px 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}

.month-label {
  font-size: 1.1rem;
  font-weight: 800;
  color: #111827;
  letter-spacing: -0.02em;
}

.year-label {
  font-size: 0.8rem;
  color: #6B7280;
  font-weight: 600;
}

/* ─── Calendar Grid ─── */
.calendar-grid-wrapper {
  background: white;
  border: 1px solid #E5E7EB;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0,0,0,0.05);
}

.day-labels-row {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  background: #F9FAFB;
  border-bottom: 1px solid #E5E7EB;
}

.day-label {
  padding: 10px 0;
  text-align: center;
  font-size: 0.75rem;
  font-weight: 700;
  color: #6B7280;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.days-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
}

.day-cell {
  position: relative;
  min-height: 72px;
  padding: 8px 10px;
  border-right: 1px solid #F3F4F6;
  border-bottom: 1px solid #F3F4F6;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.day-cell:nth-child(7n) { border-right: none; }

.day-cell:hover:not(.day-empty) {
  background: #F9FAFB;
}

.day-empty {
  cursor: default;
  background: #FAFAFA;
}

.day-today .day-number {
  background: #DC2626;
  color: white !important;
  border-radius: 50%;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 0.85rem;
}

.day-selected {
  background: #FEF2F2 !important;
}

.day-selected .day-number {
  color: #DC2626;
  font-weight: 800;
}

.day-has-events {
  background: #FFFBEB;
}

.day-number {
  font-size: 0.85rem;
  font-weight: 600;
  color: #374151;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
}

/* ─── Event Dots ─── */
.event-dots {
  display: flex;
  flex-wrap: wrap;
  gap: 3px;
  margin-top: 4px;
}

.event-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  display: inline-block;
}

.event-dot-more {
  width: auto;
  height: auto;
  border-radius: 0;
  font-size: 0.6rem;
  font-weight: 700;
  color: #9CA3AF;
}

.dot-regular, .type-regular .event-dot { background: #DC2626; }
.dot-special, .type-special .event-dot { background: #7C3AED; }
.dot-monthly, .type-monthly .event-dot { background: #059669; }
.dot-other,   .type-other   .event-dot { background: #D97706; }

/* ─── Legend ─── */
.legend-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  display: inline-block;
}

.dot-regular { background: #DC2626; }
.dot-special { background: #7C3AED; }
.dot-monthly { background: #059669; }
.dot-other   { background: #D97706; }

/* ─── Event Cards ─── */
.events-accent {
  width: 4px;
  height: 24px;
  background: #DC2626;
  border-radius: 2px;
  flex-shrink: 0;
}

.event-card {
  display: block;
  border-radius: 16px;
  overflow: hidden;
  background: white;
  border: 1px solid #E5E7EB;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.event-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.1);
}

.event-card-img {
  position: relative;
  height: 160px;
  overflow: hidden;
}

.type-regular { background: linear-gradient(135deg, #DC2626, #991B1B); }
.type-special { background: linear-gradient(135deg, #7C3AED, #5B21B6); }
.type-monthly { background: linear-gradient(135deg, #059669, #047857); }
.type-other   { background: linear-gradient(135deg, #D97706, #B45309); }

.event-poster {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: brightness(0.65);
  transition: transform 0.4s ease;
}

.event-card:hover .event-poster {
  transform: scale(1.05);
}

.event-card-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%);
}

.event-date-badge {
  position: absolute;
  top: 12px;
  left: 12px;
  background: rgba(255,255,255,0.15);
  backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,0.3);
  border-radius: 10px;
  padding: 6px 10px;
  text-align: center;
  color: white;
}

.badge-day {
  display: block;
  font-size: 1.2rem;
  font-weight: 900;
  line-height: 1;
}

.badge-month {
  display: block;
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  opacity: 0.9;
}

.event-type-chip {
  position: absolute;
  bottom: 10px;
  right: 10px;
  font-size: 0.6rem !important;
  letter-spacing: 0.05em;
}

.event-card-body {
  padding: 14px 16px;
}

.event-title {
  font-size: 0.9rem;
  font-weight: 800;
  color: #111827;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  letter-spacing: -0.01em;
}

/* ─── Selected Events ─── */
.selected-events {
  animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ─── Responsive ─── */
@media (max-width: 600px) {
  .day-cell {
    min-height: 52px;
    padding: 6px 4px;
  }

  .day-number {
    font-size: 0.75rem;
    width: 24px;
    height: 24px;
  }

  .day-today .day-number {
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
  }

  .event-dot {
    width: 5px;
    height: 5px;
  }
}
</style>
