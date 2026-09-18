<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => 'Tentang Kami',
                'slug' => 'tentang-kami',
                'content' => '<h2>Melangkah Bersama Jalan Bareng</h2>
<p>Jalan Bareng bermula dari niat sederhana: berbagi kesenangan menyusuri sudut-sudut kota dengan berjalan kaki sambil menyapa wajah-wajah baru, tanpa beban ekspektasi. Meski rute yang dilalui kerap jauh dari kata ramah pejalan kaki, gerakan ini terbukti telah berdenyut dan melangkah bersama selama lebih dari dua tahun di Makassar.</p>

<p>Menariknya, mayoritas peserta yang mendaftar merupakan para perantau yang sempat merasa asing di kota ini. Lewat ruang ini, mereka justru menemukan teman hingga sahabat dalam keseharian. Mirip adegan ikonik film saat tak sengaja berpapasan dengan orang asing di trotoar, perjumpaan yang organik tanpa tuntutan ini memberi kebebasan bagi setiap orang untuk tampil jujur dan menjadi diri sendiri apa adanya.</p>

<h3>Pendekatan Walking Tour & Pemberdayaan</h3>
<p>Jalan Bareng merupakan komunitas pemberdayaan anak muda dan masyarakat dalam pengembangan diri & kelompok, tata kota, upaya ramah iklim, serta kreativitas.</p>
<p>Pendekatan utama kegiatan kami adalah <strong>Walking Tour</strong>. Kami merespon segmen kegiatan baru dan unik di mana pendekatan kegiatan anak muda mulai bergeser: dari yang awalnya hiburan konsumtif, kini beralih ke kegiatan produktif, kebugaran raga, dan kesehatan mental.</p>

<h3>6 Pilar Ekosistem Jalan Bareng</h3>
<ul>
<li><strong>Explore Bareng:</strong> Media aktivitas Jalan Bareng mengenai kegiatan berjalan kaki, eksplorasi sudut kota, dan social gathering.</li>
<li><strong>Makan Bareng:</strong> Komunitas makan bersama yang mengangkat kisah sejarah, budaya, dan narasi di balik kuliner lokal.</li>
<li><strong>Diskusi Buku Bareng:</strong> Komunitas literasi yang menghidupkan percakapan gagasan di ruang-ruang terbuka kota.</li>
<li><strong>Lanjut Bergerak:</strong> Komunitas sosial kerelawanan (volunteering) yang berfokus pada pemenuhan hak dan kebahagiaan anak.</li>
<li><strong>Djari Djemari Makassar:</strong> Upaya pemberdayaan anggota komunitas dalam bentuk kerajinan tangan (handcrafting) dan kriya kreatif.</li>
<li><strong>Jalan Bareng Creative Space:</strong> Ruang temu bersama untuk aktivasi ruang, instalasi seni, workshop, talkshow, dan kolaborasi kreatif.</li>
</ul>

<h3>Jejaring Wilayah Aktivasi</h3>
<p>Saat ini, inisiatif Jalan Bareng telah melangkah dan hadir di berbagai kota dan kabupaten:</p>
<ul>
<li>Kota Makassar</li>
<li>Kota Palopo</li>
<li>Kota Jakarta Selatan</li>
<li>Kabupaten Gowa</li>
<li>Kabupaten Bone</li>
</ul>

<h3>Kemitraan & Dampak Nyata</h3>
<p>Hingga kini, Jalan Bareng telah menaungi <strong>1.000+ anggota aktif</strong>, <strong>10.000+ pendaftar di database</strong>, serta berkolaborasi dengan <strong>80+ mitra strategis</strong> yang mencakup pemerintah, kementerian, institusi pendidikan, korporasi & brand global (seperti Infinix), BUMN, hingga organisasi internasional.</p>
<p><em>#menelusurikotalebihlambat</em></p>',
                'meta_description' => 'Tentang Jalan Bareng: Komunitas pejalan kaki, walking tour produktif, dan ruang pemberdayaan masyarakat.',
                'is_published' => true,
                'order' => 1,
            ],
            [
                'title' => 'Panduan & Etika Komunitas',
                'slug' => 'panduan-komunitas',
                'content' => '<h2>Panduan dan Etika Pejalan Kaki Jalan Bareng</h2>
<p>Agar setiap langkah penjelajahan bersama terasa aman, nyaman, dan menyenangkan bagi semua orang, berikut panduan sederhana yang kami junjung bersama:</p>

<h3>1. Perlengkapan yang Dianjurkan</h3>
<ul>
<li><strong>Sepatu Nyaman:</strong> Gunakan sepatu jalan santai atau sneakers dengan bantalan empuk.</li>
<li><strong>Tumbler Air Minum:</strong> Bawalah botol minum pribadi untuk mengurangi sampah botol plastik sekali pakai.</li>
<li><strong>Perlindungan Cuaca:</strong> Bawa topi, payung lipat, atau jas hujan saku bila cuaca mendung.</li>
<li><strong>Pakaian Menyerap Keringat:</strong> Kenakan pakaian yang longgar dan nyaman untuk beraktivitas luar ruangan.</li>
</ul>

<h3>2. Etika Melintasi Perkampungan dan Gang Sempit</h3>
<ul>
<li><strong>Sapaan Santun:</strong> Berikan senyuman dan sapaan ramah (seperti "permisi", "tabe", atau "selamat pagi") kepada warga yang sedang duduk di teras.</li>
<li><strong>Hormati Privasi:</strong> Hindari mengarahkan kamera langsung ke dalam jendela rumah warga tanpa izin lisan.</li>
<li><strong>Jaga Jalur Jalan:</strong> Berjalanlah beriringan dua orang agar tidak menutup akses jalan bagi warga atau pengendara sepeda onthel.</li>
<li><strong>Kelola Sampah Mandiri:</strong> Kantongi kembali sampah kemasan Anda hingga menemukan tempat sampah yang layak.</li>
</ul>

<h3>3. Solidaritas dan Inklusi</h3>
<ul>
<li>Sesuaikan tempo jalan dengan kawan yang paling lambat. Jalan Bareng bukan perlombaan lari, melainkan kebersamaan.</li>
<li>Berikan bantuan kepada kawan difabel atau lansia saat melewati tanjakan, ramp, atau jalan yang kurang rata.</li>
</ul>',
                'meta_description' => 'Panduan dan etika berjalan kaki bersama komunitas Jalan Bareng.',
                'is_published' => true,
                'order' => 2,
            ],
            [
                'title' => 'Tanya Jawab (FAQ)',
                'slug' => 'faq',
                'content' => '<h2>Tanya Jawab Seputar Jalan Bareng</h2>
<p>Berikut kumpulan pertanyaan yang sering ditanyakan oleh kawan-kawan yang baru pertama kali ingin bergabung:</p>

<h3>Apakah kegiatan Jalan Bareng berbayar?</h3>
<p>Seluruh agenda jalan santai rutin mingguan gratis dan terbuka untuk umum. Pada beberapa agenda khusus (seperti workshop kriya atau makan bersama), sistem yang digunakan adalah patungan bahan atau split-bill ramah kantong.</p>

<h3>Bagaimana cara mendaftar untuk ikut jalan santai?</h3>
<p>Anda cukup melihat jadwal di menu Event atau pantau pengumuman titik kumpul di akun media sosial chapter kota Anda. Datanglah tepat waktu di titik kumpul yang telah ditentukan.</p>

<h3>Apakah pemula yang jarang berolahraga kuat mengikutinya?</h3>
<p>Sangat kuat! Rute jalan santai kami dirancang dengan jarak rata-rata 3 sampai 4 kilometer dengan tempo santai dan banyak jeda istirahat untuk mendengarkan cerita lokal.</p>

<h3>Bolehkah mengajak anak-anak atau orang tua?</h3>
<p>Sangat boleh. Banyak peserta kami yang membawa keluarga, anak kecil dengan kereta dorong, maupun orang tua. Tim fasilitator selalu siap mendampingi.</p>

<h3>Bagaimana cara membagikan cerita perjalanan di web ini?</h3>
<p>Anda bisa membuka halaman Cerita Jalan Bareng dan klik tombol "Kirim Tulisan". Tulisan Anda akan ditinjau oleh kurator komunitas sebelum dipublikasikan di web.</p>',
                'meta_description' => 'Tanya jawab seputar kegiatan, pendaftaran, dan rute komunitas pejalan kaki Jalan Bareng.',
                'is_published' => true,
                'order' => 3,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'syarat-ketentuan',
                'content' => '<h2>Syarat dan Ketentuan Penggunaan</h2>
<p>Terakhir diperbarui: ' . date('d F Y') . '</p>

<h3>1. Penerimaan Ketentuan</h3>
<p>Dengan mengakses platform Jalan Bareng, Anda menyetujui untuk terikat dengan ketentuan penggunaan dan nilai-nilai komunitas kami.</p>

<h3>2. Penggunaan Platform</h3>
<p>Platform ini disediakan sebagai sarana berbagi rute, catatan perjalanan, dan agenda komunitas. Pengguna diharapkan menjaga kerukunan, tidak mengunggah konten yang melanggar hukum atau merugikan orang lain, serta menghormati privasi sesama anggota.</p>

<h3>3. Keselamatan Diri dalam Kegiatan Terbuka</h3>
<p>Setiap peserta kegiatan jalan kaki bertanggung jawab atas kondisi fisik dan keselamatan dirinya masing-masing. Panitia komunitas menyediakan pemandu rute dan pertolongan pertama dasar, namun peserta disarankan menjaga stamina dan mematuhi rambu lalu lintas jalan raya.</p>',
                'meta_description' => 'Syarat dan ketentuan penggunaan platform komunitas Jalan Bareng.',
                'is_published' => true,
                'order' => 4,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'content' => '<h2>Kebijakan Privasi</h2>
<p>Terakhir diperbarui: ' . date('d F Y') . '</p>

<h3>1. Informasi Akun</h3>
<p>Informasi yang kami simpan saat pendaftaran (nama, email, tautan media sosial profil) hanya digunakan untuk autentikasi dan atribusi karya tulisan yang Anda kirimkan.</p>

<h3>2. Perlindungan Data</h3>
<p>Kami tidak akan memperjualbelikan atau membagikan data pribadi Anda kepada pihak ketiga untuk kepentingan komersial tanpa persetujuan Anda.</p>',
                'meta_description' => 'Kebijakan privasi platform Jalan Bareng: Bagaimana data akun Anda dijaga dengan aman.',
                'is_published' => true,
                'order' => 5,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('   - ' . count($pages) . ' static pages created successfully');
    }
}
