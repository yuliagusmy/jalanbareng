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
                'content' => '<h2>Tentang Jalan Bareng</h2>
<p>Jalan Bareng adalah platform komunitas yang menghubungkan para pecinta jalan kaki dan eksplorasi destinasi lokal. Kami percaya bahwa setiap langkah adalah petualangan baru yang menanti untuk ditemukan.</p>

<h3>Visi Kami</h3>
<p>Menjadi platform terdepan yang menginspirasi masyarakat untuk lebih aktif, sehat, dan mengenal keindahan lokal melalui kegiatan jalan kaki bersama.</p>

<h3>Misi Kami</h3>
<ul>
<li>Memfasilitasi kegiatan jalan kaki yang aman dan menyenangkan</li>
<li>Memperkenalkan destinasi-destinasi menarik di berbagai kota</li>
<li>Membangun komunitas yang solid dan saling mendukung</li>
<li>Mendorong gaya hidup sehat melalui aktivitas outdoor</li>
</ul>

<h3>Bergabunglah Bersama Kami</h3>
<p>Jadilah bagian dari komunitas Jalan Bareng dan temukan pengalaman berjalan kaki yang tak terlupakan!</p>',
                'meta_description' => 'Tentang Jalan Bareng - Platform komunitas pecinta jalan kaki dan eksplorasi destinasi lokal',
                'is_published' => true,
                'order' => 1,
            ],
            [
                'title' => 'Syarat & Ketentuan',
                'slug' => 'syarat-ketentuan',
                'content' => '<h2>Syarat dan Ketentuan Penggunaan</h2>
<p>Terakhir diperbarui: ' . date('d F Y') . '</p>

<h3>1. Penerimaan Ketentuan</h3>
<p>Dengan mengakses dan menggunakan platform Jalan Bareng, Anda menyetujui untuk terikat dengan syarat dan ketentuan berikut.</p>

<h3>2. Penggunaan Platform</h3>
<p>Platform ini disediakan untuk memfasilitasi kegiatan jalan kaki dan berbagi informasi destinasi. Pengguna diharapkan untuk:</p>
<ul>
<li>Menggunakan platform dengan bijak dan bertanggung jawab</li>
<li>Tidak menyebarkan konten yang melanggar hukum atau merugikan pihak lain</li>
<li>Menghormati hak privasi pengguna lain</li>
<li>Menjaga keamanan akun pribadi</li>
</ul>

<h3>3. Konten Pengguna</h3>
<p>Pengguna bertanggung jawab penuh atas konten yang mereka unggah, termasuk foto, komentar, dan informasi destinasi.</p>

<h3>4. Keikutsertaan dalam Event</h3>
<p>Peserta event bertanggung jawab atas keselamatan diri sendiri. Jalan Bareng tidak bertanggung jawab atas cedera atau kerugian yang terjadi selama event.</p>

<h3>5. Perubahan Ketentuan</h3>
<p>Kami berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Perubahan akan diberitahukan melalui platform.</p>

<h3>6. Kontak</h3>
<p>Jika Anda memiliki pertanyaan mengenai syarat dan ketentuan ini, silakan hubungi kami melalui email atau media sosial kami.</p>',
                'meta_description' => 'Syarat dan ketentuan penggunaan platform Jalan Bareng',
                'is_published' => true,
                'order' => 2,
            ],
            [
                'title' => 'Kebijakan Privasi',
                'slug' => 'kebijakan-privasi',
                'content' => '<h2>Kebijakan Privasi</h2>
<p>Terakhir diperbarui: ' . date('d F Y') . '</p>

<h3>1. Informasi yang Kami Kumpulkan</h3>
<p>Kami mengumpulkan informasi yang Anda berikan saat:</p>
<ul>
<li>Mendaftar akun (nama, email, foto profil)</li>
<li>Membuat konten (destinasi, komentar, foto)</li>
<li>Berpartisipasi dalam event</li>
<li>Menggunakan fitur-fitur platform</li>
</ul>

<h3>2. Penggunaan Informasi</h3>
<p>Informasi yang kami kumpulkan digunakan untuk:</p>
<ul>
<li>Menyediakan dan meningkatkan layanan platform</li>
<li>Mengelola akun pengguna</li>
<li>Mengirimkan notifikasi terkait event dan aktivitas</li>
<li>Menganalisis penggunaan platform untuk perbaikan</li>
</ul>

<h3>3. Perlindungan Data</h3>
<p>Kami berkomitmen untuk melindungi data pribadi Anda dengan menerapkan langkah-langkah keamanan yang sesuai.</p>

<h3>4. Berbagi Informasi</h3>
<p>Kami tidak akan menjual atau membagikan informasi pribadi Anda kepada pihak ketiga tanpa persetujuan Anda, kecuali diwajibkan oleh hukum.</p>

<h3>5. Cookie</h3>
<p>Platform kami menggunakan cookie untuk meningkatkan pengalaman pengguna dan menganalisis traffic website.</p>

<h3>6. Hak Pengguna</h3>
<p>Anda memiliki hak untuk:</p>
<ul>
<li>Mengakses data pribadi Anda</li>
<li>Memperbarui atau menghapus informasi</li>
<li>Menarik persetujuan penggunaan data</li>
<li>Mengajukan keluhan terkait privasi</li>
</ul>

<h3>7. Perubahan Kebijakan</h3>
<p>Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan akan diberitahukan melalui platform.</p>

<h3>8. Hubungi Kami</h3>
<p>Jika Anda memiliki pertanyaan tentang kebijakan privasi ini, silakan hubungi kami.</p>',
                'meta_description' => 'Kebijakan privasi platform Jalan Bareng - Bagaimana kami mengumpulkan dan melindungi data Anda',
                'is_published' => true,
                'order' => 3,
            ],
        ];

        foreach ($pages as $pageData) {
            Page::updateOrCreate(
                ['slug' => $pageData['slug']],
                $pageData
            );
        }

        $this->command->info('   - 3 pages created successfully');
    }
}
