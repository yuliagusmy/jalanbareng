/**
 * Centralized lazy loading definitions for heavy components
 * Improves initial bundle size by code-splitting these components
 */

import { defineAsyncComponent } from 'vue'

/**
 * Lazy load component with loading & error states
 */
const createLazyComponent = (loader: () => Promise<any>, options = {}) => {
  return defineAsyncComponent({
    loader,
    loadingComponent: undefined, // Can add loading skeleton here
    errorComponent: undefined, // Can add error component here
    delay: 200, // Delay before showing loading component
    timeout: 10000, // Timeout for loading
    ...options,
  })
}

// Map Components (Heavy - Leaflet/MapLibre)
export const LazyDestinationMapViewer = createLazyComponent(
  () => import('~/components/destinations/DestinationMapViewer.vue')
)

export const LazyDestinationMobileMapView = createLazyComponent(
  () => import('~/components/destinations/DestinationMobileMapView.vue')
)

export const LazyWalkingRouteMapViewer = createLazyComponent(
  () => import('~/components/events/WalkingRouteMapViewer.vue')
)

export const LazyRouteMapViewer = createLazyComponent(
  () => import('~/components/events/RouteMapViewer.vue')
)

export const LazyRouteMapEditor = createLazyComponent(
  () => import('~/components/events/RouteMapEditor.vue')
)

export const LazyGoogleMapRouteViewer = createLazyComponent(
  () => import('~/components/events/GoogleMapRouteViewer.vue')
)

// Modals & Dialogs (Heavy UI)
export const LazyLiveGpsRouteTrackerModal = createLazyComponent(
  () => import('~/components/events/LiveGpsRouteTrackerModal.vue')
)

export const LazySubmitStoryDialog = createLazyComponent(
  () => import('~/components/stories/SubmitStoryDialog.vue')
)

export const LazyProfileEditDialog = createLazyComponent(
  () => import('~/components/profile/ProfileEditDialog.vue')
)

export const LazyLandmarkDetailModal = createLazyComponent(
  () => import('~/components/destinations/LandmarkDetailModal.vue')
)

export const LazyPartnerDetailDialog = createLazyComponent(
  () => import('~/components/mitra/PartnerDetailDialog.vue')
)

// Editors (Heavy - Tiptap)
export const LazyTiptapEditor = createLazyComponent(
  () => import('~/components/common/TiptapEditor.vue')
)

// Heavy Sections (Can be lazy loaded below fold)
export const LazyContributorPointsWallet = createLazyComponent(
  () => import('~/components/profile/ContributorPointsWallet.vue')
)

export const LazyStoryCommentSection = createLazyComponent(
  () => import('~/components/stories/StoryCommentSection.vue')
)

// Directory Sheets
export const LazyDestinationDirectorySheet = createLazyComponent(
  () => import('~/components/destinations/DestinationDirectorySheet.vue')
)

// For future use - add more heavy components here
export const lazyComponents = {
  // Maps
  DestinationMapViewer: LazyDestinationMapViewer,
  DestinationMobileMapView: LazyDestinationMobileMapView,
  WalkingRouteMapViewer: LazyWalkingRouteMapViewer,
  RouteMapViewer: LazyRouteMapViewer,
  RouteMapEditor: LazyRouteMapEditor,
  GoogleMapRouteViewer: LazyGoogleMapRouteViewer,
  
  // Modals
  LiveGpsRouteTrackerModal: LazyLiveGpsRouteTrackerModal,
  SubmitStoryDialog: LazySubmitStoryDialog,
  ProfileEditDialog: LazyProfileEditDialog,
  LandmarkDetailModal: LazyLandmarkDetailModal,
  PartnerDetailDialog: LazyPartnerDetailDialog,
  
  // Editors
  TiptapEditor: LazyTiptapEditor,
  
  // Sections
  ContributorPointsWallet: LazyContributorPointsWallet,
  StoryCommentSection: LazyStoryCommentSection,
  DestinationDirectorySheet: LazyDestinationDirectorySheet,
}
