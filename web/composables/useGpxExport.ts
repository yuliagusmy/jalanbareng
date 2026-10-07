/**
 * useGpxExport — generates and downloads a GPX file from a route array.
 * GPX (GPS Exchange Format) can be opened in Strava, Komoot, Google Maps, etc.
 */

interface RoutePoint {
  lat: number
  lng: number
  ele?: number   // elevation in meters (optional)
  name?: string  // waypoint name (optional)
}

export function useGpxExport() {
  /**
   * Generate GPX XML string from route points.
   */
  const buildGpx = (
    points: RoutePoint[],
    trackName: string,
    description: string = ''
  ): string => {
    const now = new Date().toISOString()

    const trkpts = points
      .map((p, i) => {
        const ele = p.ele !== undefined ? `\n        <ele>${p.ele.toFixed(1)}</ele>` : ''
        const time = `\n        <time>${now}</time>`
        return `      <trkpt lat="${p.lat.toFixed(7)}" lon="${p.lng.toFixed(7)}">${ele}${time}\n      </trkpt>`
      })
      .join('\n')

    // Waypoints for start and finish
    const wpts =
      points.length >= 2
        ? [
            `  <wpt lat="${points[0].lat.toFixed(7)}" lon="${points[0].lng.toFixed(7)}">\n    <name>Start 🚩</name>\n  </wpt>`,
            `  <wpt lat="${points[points.length - 1].lat.toFixed(7)}" lon="${points[points.length - 1].lng.toFixed(7)}">\n    <name>Finish 🏁</name>\n  </wpt>`,
          ].join('\n')
        : ''

    return `<?xml version="1.0" encoding="UTF-8"?>
<gpx version="1.1" creator="Jalan Bareng"
  xmlns="http://www.topografix.com/GPX/1/1"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd">
  <metadata>
    <name>${escapeXml(trackName)}</name>
    <desc>${escapeXml(description)}</desc>
    <author><name>Jalan Bareng</name></author>
    <time>${now}</time>
  </metadata>
${wpts}
  <trk>
    <name>${escapeXml(trackName)}</name>
    <type>walking</type>
    <trkseg>
${trkpts}
    </trkseg>
  </trk>
</gpx>`
  }

  /**
   * Trigger browser download of the GPX file.
   */
  const downloadGpx = (
    points: RoutePoint[],
    trackName: string,
    description: string = ''
  ) => {
    if (!points || points.length === 0) return

    const gpxContent = buildGpx(points, trackName, description)
    const blob = new Blob([gpxContent], { type: 'application/gpx+xml' })
    const url = URL.createObjectURL(blob)

    const safeFilename = trackName
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .substring(0, 60)

    const a = document.createElement('a')
    a.href = url
    a.download = `jalan-bareng-${safeFilename}.gpx`
    document.body.appendChild(a)
    a.click()
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }

  return { downloadGpx, buildGpx }
}

function escapeXml(str: string): string {
  return str
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;')
}
