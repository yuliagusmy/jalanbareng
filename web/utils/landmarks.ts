export interface LandmarkItem {
  id: string
  name: string
  tagline: string
  city: string
  category: string
  categoryIcon: string
  suitability: string
  walkDistance: string
  bestTime: string
  shortDesc: string
  historyDesc: string
  startPoint: string
  routeSteps: string
  communityTips: string
  facilities: string[]
  image: string
  gmapsUrl?: string
}

export const LANDMARKS_DATA: LandmarkItem[] = [
  {
    id: 'pantai-losari',
    name: 'Pantai Losari & Anjungan Etnis',
    tagline: 'Episentrum senja dan ruang publik legendaris pesisir Selat Makassar',
    city: 'Makassar',
    category: 'Pesisir & Sunset',
    categoryIcon: 'mdi-weather-sunset',
    suitability: 'Akses Trotoar Ramah',
    walkDistance: '2.2 km (~2.800 langkah)',
    bestTime: '16.30 - 18.30 WITA',
    shortDesc: 'Pusat promenade pejalan kaki terpanjang di Makassar dengan pemandangan sunset dan deretan anjungan budaya.',
    historyDesc: 'Pantai Losari sejak dekade 1940-an telah menjadi ruang temu warga Makassar. Dihiasi anjungan representasi suku Bugis, Makassar, Mandar, dan Toraja. Jalur trotoar pesisirnya luas, datar, dan menghubungkan pusat kota dengan kawasan modern CPI.',
    startPoint: 'Depan Hotel Aryaduta Makassar / Monumen Mandala Makassar',
    routeSteps: 'Menyusuri Jl. Penghibur -> Anjungan Bugis-Makassar -> Masjid Terapung Amirul Mukminin -> Jembatan CPI.',
    communityTips: 'Waktu terbaik adalah saat senja tiba ketika lampu-lampu kapal nelayan mulai berpendar dan angin laut berembus sejuk.',
    facilities: ['Toilet Umum', 'Mushola Terapung', 'Penjual Pisang Epe Legendaris', 'Akses Kursi Roda', 'Spot Foto Ikonik'],
    image: '/images/hero/walk_3.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Pantai+Losari+Makassar'
  },
  {
    id: 'fort-rotterdam',
    name: 'Benteng Rotterdam (Ujung Pandang)',
    tagline: 'Cagar budaya abad ke-16 peninggalan Kerajaan Gowa-Tallo',
    city: 'Makassar',
    category: 'Heritage & Sejarah',
    categoryIcon: 'mdi-castle',
    suitability: 'Jalur Pedestrian Rindang',
    walkDistance: '1.5 km (~1.900 langkah)',
    bestTime: '07.30 - 10.00 WITA',
    shortDesc: 'Benteng pertahanan berbentuk penyu dengan arsitektur pualam kolonial Belanda dan Museum La Galigo.',
    historyDesc: 'Awalnya dibangun Raja Gowa IX I Manrigau Daeng Bonto Karaeng Lakiung pada 1545 dengan arsitektur tanah liat, kemudian disempurnakan batu padas. Area dalamnya memiliki halaman rumput asri nan teduh yang sangat nyaman untuk jalan santai pagi.',
    startPoint: 'Dermaga Kayu Tradisional Bangkoa / Depan Gerbang Utama Rotterdam',
    routeSteps: 'Keliling pelataran dalam benteng -> Naik ke dinding bastion pemantau laut -> Menelusuri koridor Jl. Ujung Pandang.',
    communityTips: 'Cocok untuk morning walk akhir pekan sebelum cuaca terlalu terik. Jangan lupa kunjungi Museum La Galigo untuk literasi sejarah Maritim.',
    facilities: ['Museum Sejarah', 'Halaman Teduh', 'Pojok Literasi', 'Area Parkir Luas', 'Kedai Kopi Bersejarah'],
    image: '/images/hero/walk_4.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Benteng+Rotterdam+Makassar'
  },
  {
    id: 'masjid-99-kubah',
    name: 'Masjid 99 Kubah & CPI',
    tagline: 'Karya arsitektur futuristik dengan promenade pesisir termegah',
    city: 'Makassar',
    category: 'Arsitektur & Landmark',
    categoryIcon: 'mdi-mosque',
    suitability: 'Jalur Trotoar Sangat Lebar',
    walkDistance: '2.5 km (~3.200 langkah)',
    bestTime: '06.00 - 08.00 / 17.00 WITA',
    shortDesc: 'Landmark baru berlatar 99 kubah beraneka warna dengan jalur pedestrian terbuka tepi teluk.',
    historyDesc: 'Dirancang oleh arsitek kenamaan Ridwan Kamil, landmark ini menjadi simbol baru keterbukaan Kota Makassar. Memiliki pelataran air mancur menari, jembatan Tongkonan, serta jalur trotoar tepi kanal yang sangat ramah pejalan kaki.',
    startPoint: 'Jembatan Masuk Kawasan CPI (Centre Point of Indonesia)',
    routeSteps: 'Menyeberang jembatan CPI -> Melintasi Sunset Quay -> Menuju pelataran utama Masjid 99 Kubah -> Bundaran CPI.',
    communityTips: 'Rute ini favorit untuk jogging pagi atau jalan santai sore karena bebas polusi knalpot di koridor tamannya.',
    facilities: ['Kran Air Minum', 'Tempat Ibadah Megah', 'Jalur Sepeda & Jogging', 'Taman Tematik', 'Toilet Bersih'],
    image: '/images/hero/walk_2.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Masjid+99+Kubah+Makassar'
  },
  {
    id: 'pelabuhan-paotere',
    name: 'Pelabuhan Tradisional Paotere',
    tagline: 'Jejak kejayaan maritim perahu layar Phinisi sejak abad ke-14',
    city: 'Makassar',
    category: 'Kemaritiman & Budaya',
    categoryIcon: 'mdi-ferry',
    suitability: 'Eksplorasi Dermaga Kayu',
    walkDistance: '1.2 km (~1.500 langkah)',
    bestTime: '06.30 - 08.30 WITA',
    shortDesc: 'Menyaksikan aktivitas sandar perahu layar tradisional phinisi dan denyut kehidupan warga pesisir utara.',
    historyDesc: 'Paotere adalah salah satu pelabuhan rakyat tertua di Indonesia yang masih aktif beroperasi. Pejalan kaki dapat merasakan nuansa otentik tawar-menawar hasil laut, aroma garam laut, dan deretan tiang kapal kayu yang megah menjulang.',
    startPoint: 'Gerbang TPI Paotere (Tempat Pelelangan Ikan)',
    routeSteps: 'Menyusuri lorong pasar ikan tradisional -> Dermaga tambat perahu phinisi -> Mercusuar kecil ujung dermaga.',
    communityTips: 'Gunakan alas kaki anti selip karena beberapa lantai dermaga basah oleh air laut. Bawa kamera untuk memotret siluet kapal kayu.',
    facilities: ['Pasar Ikan Segar', 'Warung Bakar Ikan Tradisional', 'Warung Kopi Nelayan', 'Spot Fotografi Street'],
    image: '/images/hero/walk_6.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Pelabuhan+Paotere+Makassar'
  },
  {
    id: 'pecinan-somba-opu',
    name: 'Koridor Heritage Pecinan & Somba Opu',
    tagline: 'Kawasan niaga tertua, klenteng bersejarah, dan aroma kuliner legendaris',
    city: 'Makassar',
    category: 'Pecinan & Kuliner',
    categoryIcon: 'mdi-storefront-outline',
    suitability: 'Trotoar Kota Tua',
    walkDistance: '1.8 km (~2.300 langkah)',
    bestTime: '08.00 - 11.00 / 16.00 WITA',
    shortDesc: 'Menyusuri gang-gang kota tua, pengrajin perak emas tradisional, dan deretan kuliner autentik.',
    historyDesc: 'Jalan Somba Opu dan Jalan Sulawesi adalah jantung sejarah multikultural Makassar. Di sini berbaur harmonis komunitas Tionghoa, Bugis, Makassar, dan Melayu sejak ratusan tahun silam, melahirkan kuliner legendaris seperti Coto dan Kopi Susu Tarik tempo dulu.',
    startPoint: 'Perempatan Jl. Ahmad Yani / Jl. Sulawesi',
    routeSteps: 'Menyusuri koridor Klenteng Ibu Agung Bahari -> Masuk Jl. Somba Opu -> Mampir di toko cinderamata perak.',
    communityTips: 'Cocok untuk jalan santai sambil wisata kuliner (walking food tour). Jangan lewatkan kopi legendaris di rute ini.',
    facilities: ['Toko Suvenir Khas', 'Kedai Kuliner Legendaris', 'Klenteng Cagar Budaya', 'Atm Center'],
    image: '/images/hero/walk_1.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Jalan+Somba+Opu+Makassar'
  },
  {
    id: 'lapangan-karebosi',
    name: 'Kawasan Hijau Lapangan Karebosi',
    tagline: 'Titik nol kilometer dan paru-paru hijau di jantung Kota Daeng',
    city: 'Makassar',
    category: 'Taman Kota & Olahraga',
    categoryIcon: 'mdi-tree-outline',
    suitability: 'Lintasan Pejalan Teduh',
    walkDistance: '1.4 km (~1.800 langkah)',
    bestTime: '06.00 - 08.30 / 16.30 WITA',
    shortDesc: 'Pusat olahraga warga kota dengan rimbunan pohon trembesi tua dan akses belanja bawah tanah Karebosi Link.',
    historyDesc: 'Karebosi menyimpan nilai sakral sejarah Kerajaan Gowa-Tallo (Kanro Tujua) dan kini bertransformasi menjadi ruang interaksi sosial terbuka paling ramai di Makassar. Lintasan joggingnya terawat dan teduh dari terik sinar matahari.',
    startPoint: 'Halte / Pintu Masuk Depan Jl. Ahmad Yani',
    routeSteps: 'Mengitari track lingkar luar pepohonan trembesi -> Area tugu -> Terowongan pedestrian Karebosi Link.',
    communityTips: 'Pilihan terbaik untuk berjalan santai di hari kerja karena lokasinya tepat di tengah pusat perkantoran dan bisnis.',
    facilities: ['Jogging Track Karet', 'Toilet Umum', 'Karebosi Link Underground', 'Area Senam Warga', 'Penyewaan Skuter'],
    image: '/images/hero/walk_8.jpg',
    gmapsUrl: 'https://maps.google.com/?q=Lapangan+Karebosi+Makassar'
  }
]
