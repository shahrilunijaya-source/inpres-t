<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Static portal content pages (Pengenalan, Soalan Lazim, Direktori Cawangan).
 *
 * Content is informational, modelled on publicly known JPN facts. This is a
 * prototype — the "Prototaip" disclaimer in the footer applies. For the
 * authoritative source, each page links out to jpn.gov.my.
 */
class ContentController extends Controller
{
    public function pengenalan(): View
    {
        return view('content.pengenalan', ['nav' => '']);
    }

    public function soalanLazim(): View
    {
        return view('content.soalan-lazim', ['nav' => '', 'faqs' => $this->faqs()]);
    }

    public function direktoriCawangan(): View
    {
        return view('content.direktori-cawangan', ['nav' => '', 'cawangan' => $this->cawangan()]);
    }

    public function perkhidmatan(): View
    {
        return view('content.perkhidmatan', ['nav' => '', 'groups' => $this->services()]);
    }

    /**
     * Full InPreS service catalogue, grouped. `built` services are live in this
     * prototype and link to a real route; the rest are roadmap modules that open
     * the "belum tersedia" stub modal so the demo stays honest about scope.
     */
    private function services(): array
    {
        return [
            'Sijil & Rekod Sivil' => [
                ['i' => 'baby',          't' => 'Sijil Kelahiran',     'd' => 'Pendaftaran kelahiran baharu dan pengeluaran semula sijil yang hilang, dengan auto-isi MyKad melalui AI.',          'built' => true,  'url' => '/apply?type=birth'],
                ['i' => 'heart',         't' => 'Sijil Perkahwinan',   'd' => 'Pendaftaran perkahwinan bukan Islam dan pengeluaran semula sijil perkahwinan.',                                  'built' => true,  'url' => '/apply?type=marriage'],
                ['i' => 'file-x',        't' => 'Sijil Kematian',      'd' => 'Pendaftaran kematian dan permohonan salinan sijil kematian.',                                                    'built' => false],
                ['i' => 'heart-crack',   't' => 'Sijil Perceraian',    'd' => 'Pendaftaran perceraian bukan Islam di bawah Akta 164.',                                                          'built' => false],
            ],
            'Pengenalan Diri' => [
                ['i' => 'credit-card',   't' => 'MyKad',               'd' => 'Permohonan kali pertama (umur 12), pembaharuan dan penggantian Kad Pengenalan.',                                'built' => true,  'url' => '/apply?type=mykad'],
                ['i' => 'baby',          't' => 'MyKid',               'd' => 'Dokumen pengenalan kanak-kanak bawah 12 tahun.',                                                                'built' => false],
                ['i' => 'shield',        't' => 'MyTentera',           'd' => 'Kad pengenalan anggota Angkatan Tentera Malaysia.',                                                             'built' => false],
                ['i' => 'alert-triangle','t' => 'Lapor Kehilangan',    'd' => 'Laporan kehilangan MyKad sebelum permohonan penggantian.',                                                       'built' => false],
            ],
            'Kewarganegaraan & Keluarga' => [
                ['i' => 'flag',          't' => 'Kewarganegaraan',     'd' => 'Permohonan taraf warganegara dan permastautin tetap.',                                                          'built' => false],
                ['i' => 'users',         't' => 'Pengangkatan',        'd' => 'Pendaftaran anak angkat di bawah peruntukan undang-undang.',                                                     'built' => false],
                ['i' => 'git-branch',    't' => 'Carian Salasilah',    'd' => 'Carian rekod hubungan keluarga dan salasilah untuk urusan rasmi.',                                              'built' => false],
            ],
            'Status & Sokongan' => [
                ['i' => 'search-check',  't' => 'Semak Status',        'd' => 'Jejak status permohonan dengan anggaran ETA berkuasa AI — tanpa akaun.',                                        'built' => true,  'url' => '/track'],
                ['i' => 'help-circle',   't' => 'Soalan Lazim',        'd' => 'Jawapan ringkas kepada soalan yang kerap ditanya.',                                                             'built' => true,  'url' => '/soalan-lazim'],
                ['i' => 'map-pin',       't' => 'Direktori Cawangan',  'd' => 'Senarai pejabat JPN seluruh negara dan maklumat hubungan.',                                                     'built' => true,  'url' => '/direktori-cawangan'],
                ['i' => 'badge-check',   't' => 'Pengesahan Dokumen',  'd' => 'Pengesahan kesahihan dokumen JPN melalui kod QR.',                                                              'built' => false],
            ],
        ];
    }

    /** Grouped FAQ content. */
    private function faqs(): array
    {
        return [
            'Kelahiran' => [
                [
                    'q' => 'Bila kelahiran perlu didaftarkan?',
                    'a' => 'Kelahiran hendaklah didaftarkan dalam tempoh 14 hari dari tarikh lahir. Pendaftaran selepas 14 hari hingga 42 hari masih boleh dibuat di mana-mana pejabat JPN. Pendaftaran lewat melebihi 42 hari memerlukan siasatan.',
                ],
                [
                    'q' => 'Apakah dokumen diperlukan untuk daftar kelahiran?',
                    'a' => 'Surat pemberitahuan kelahiran dari hospital (Borang JPN.LM01), MyKad ibu dan bapa, serta sijil perkahwinan ibu bapa (jika ada). Melalui portal ini anda boleh mengimbas MyKad untuk auto-isi medan utama.',
                ],
                [
                    'q' => 'Bagaimana memohon salinan sijil kelahiran yang hilang?',
                    'a' => 'Mohon pengeluaran semula melalui portal ini di bahagian Mohon Dokumen, atau di kaunter JPN. Sila sediakan nombor sijil asal atau butiran peribadi pemilik sijil untuk carian.',
                ],
            ],
            'Kad Pengenalan (MyKad)' => [
                [
                    'q' => 'Pada umur berapa MyKad pertama perlu dimohon?',
                    'a' => 'Warganegara dan pemastautin tetap wajib memohon Kad Pengenalan apabila berumur 12 tahun. Pendaftaran semula (penggantian MyKid kepada MyKad) wajib dibuat pada umur 12 tahun.',
                ],
                [
                    'q' => 'MyKad hilang — apa perlu saya buat?',
                    'a' => 'Lapor kehilangan dengan segera, kemudian mohon penggantian. Bayaran penggantian dikenakan mengikut kali kehilangan. Bawa satu dokumen sokongan pengenalan diri semasa hadir ke kaunter.',
                ],
                [
                    'q' => 'Berapa lama proses pengeluaran MyKad?',
                    'a' => 'Bagi kebanyakan permohonan, MyKad siap dalam tempoh sehingga 14 hari bekerja. Status boleh disemak pada bila-bila masa melalui Semak Status di portal ini.',
                ],
            ],
            'Perkahwinan & Perceraian' => [
                [
                    'q' => 'Siapa boleh mendaftar perkahwinan di JPN?',
                    'a' => 'JPN menguruskan pendaftaran perkahwinan dan perceraian bukan Islam di bawah Akta Membaharui Undang-Undang (Perkahwinan dan Perceraian) 1976. Perkahwinan Islam didaftarkan di Jabatan Agama Islam Negeri.',
                ],
                [
                    'q' => 'Berapa tempoh notis perkahwinan?',
                    'a' => 'Notis perkahwinan perlu difailkan dan dipamerkan selama 21 hari sebelum upacara boleh dijalankan. Pasangan hendaklah berumur sekurang-kurangnya 18 tahun.',
                ],
            ],
            'Am' => [
                [
                    'q' => 'Adakah saya perlu akaun untuk menyemak status?',
                    'a' => 'Tidak. Semakan status dilindungi dengan padanan Nombor Rujukan dan nombor Kad Pengenalan pemohon — tiada pendaftaran akaun diperlukan. Maklumat hanya dipaparkan apabila kedua-dua butiran sepadan.',
                ],
                [
                    'q' => 'Adakah portal ini rasmi?',
                    'a' => 'Portal ini ialah prototaip demonstrasi. Untuk urusan dan maklumat rasmi, sila rujuk laman web rasmi JPN di jpn.gov.my atau hadir ke cawangan JPN berhampiran.',
                ],
            ],
        ];
    }

    /** State-level JPN office directory (headline contact per negeri). */
    private function cawangan(): array
    {
        return [
            ['negeri' => 'Ibu Pejabat (Putrajaya)', 'alamat' => 'Kompleks Kementerian Dalam Negeri, No. 20 Persiaran Perdana, Presint 2, 62551 Putrajaya', 'tel' => '03-8000 8000'],
            ['negeri' => 'WP Kuala Lumpur', 'alamat' => 'Menara Maybank, Jalan Tun Perak, 50050 Kuala Lumpur', 'tel' => '03-8000 8000'],
            ['negeri' => 'Selangor', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Selangor, Shah Alam', 'tel' => '03-8000 8000'],
            ['negeri' => 'Johor', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Johor, Johor Bahru', 'tel' => '03-8000 8000'],
            ['negeri' => 'Pulau Pinang', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Pulau Pinang, George Town', 'tel' => '03-8000 8000'],
            ['negeri' => 'Perak', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Perak, Ipoh', 'tel' => '03-8000 8000'],
            ['negeri' => 'Kedah', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Kedah, Alor Setar', 'tel' => '03-8000 8000'],
            ['negeri' => 'Perlis', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Perlis, Kangar', 'tel' => '03-8000 8000'],
            ['negeri' => 'Kelantan', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Kelantan, Kota Bharu', 'tel' => '03-8000 8000'],
            ['negeri' => 'Terengganu', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Terengganu, Kuala Terengganu', 'tel' => '03-8000 8000'],
            ['negeri' => 'Pahang', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Pahang, Kuantan', 'tel' => '03-8000 8000'],
            ['negeri' => 'Negeri Sembilan', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Sembilan, Seremban', 'tel' => '03-8000 8000'],
            ['negeri' => 'Melaka', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Melaka, Melaka', 'tel' => '03-8000 8000'],
            ['negeri' => 'Sabah', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Sabah, Kota Kinabalu', 'tel' => '03-8000 8000'],
            ['negeri' => 'Sarawak', 'alamat' => 'Jabatan Pendaftaran Negara Negeri Sarawak, Kuching', 'tel' => '03-8000 8000'],
            ['negeri' => 'WP Labuan', 'alamat' => 'Jabatan Pendaftaran Negara WP Labuan, Victoria', 'tel' => '03-8000 8000'],
        ];
    }
}
