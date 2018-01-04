<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBriguna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table( 'briguna', function ( Blueprint $table ) {
            $table->text( 'uid')->nullable();
            $table->text( 'uid_pemrakarsa')->nullable();
            $table->text( 'tp_produk')->nullable();
            $table->text( 'id_aplikasi')->nullable();
            $table->text( 'cif_las')->nullable();
            $table->text( 'Tgl_perkiraan_pensiun')->nullable();
            $table->text( 'Sifat_suku_bunga')->nullable();
            $table->text( 'Briguna_profesi')->nullable();
            $table->text( 'Pendapatan_profesi')->nullable();
            $table->text( 'Potongan_per_bulan')->nullable();
            $table->text( 'Plafond_briguna_existing')->nullable();
            $table->text( 'Angsuran_briguna_existing')->nullable();
            $table->text( 'Suku_bunga')->nullable();
            $table->text( 'Jangka_waktu')->nullable();
            $table->text( 'Baki_debet')->nullable();
            $table->text( 'Plafond_usulan')->nullable();
            $table->text( 'Rek_simpanan_bri')->nullable();
            $table->text( 'Riwayat_pinjaman')->nullable();
            $table->text( 'Penguasaan_cashflow')->nullable();
            $table->text( 'Payroll')->nullable();
            $table->text( 'Gaji_bersih_per_bulan')->nullable();
            $table->text( 'Maksimum_angsuran')->nullable();
            $table->text( 'Tujuan_membuka_rek')->nullable();
            $table->text( 'Briguna_smart')->nullable();
            $table->text( 'Kode_fasilitas')->nullable();
            $table->text( 'Tujuan_penggunaan_kredit')->nullable();
            $table->text( 'Penggunaan_kredit')->nullable();
            $table->text( 'Provisi_kredit')->nullable();
            $table->text( 'Biaya_administrasi')->nullable();
            $table->text( 'Penalty')->nullable();
            $table->text( 'Perusahaan_asuransi')->nullable();
            $table->text( 'Premi_asuransi_jiwa')->nullable();
            $table->text( 'Premi_beban_bri')->nullable();
            $table->text( 'Premi_beban_debitur')->nullable();
            $table->text( 'Flag_promo')->nullable();
            $table->text( 'Fid_promo')->nullable();
            $table->text( 'Pengadilan_terdekat')->nullable();
            $table->text( 'Bupln')->nullable();
            $table->text( 'Agribisnis')->nullable();
			$table->text( 'Sandi_stp')->nullable();
			$table->text( 'Sifat_kredit')->nullable();
			$table->text( 'Jenis_penggunaan')->nullable();
			$table->text( 'Sektor_ekonomi_sid')->nullable();
			$table->text( 'Jenis_kredit_lbu')->nullable();
			$table->text( 'Sifat_kredit_lbu')->nullable();
			$table->text( 'Kategori_kredit_lbu')->nullable();
			$table->text( 'Jenis_penggunaan_lbu')->nullable();
			$table->text( 'Sumber_aplikasi')->nullable();
			$table->text( 'Sektor_ekonomi_lbu')->nullable();
			$table->text( 'id_Status_gelar')->nullable();
			$table->text( 'Status_gelar')->nullable();
			$table->text( 'score')->nullable();
			$table->text( 'grade')->nullable();
			$table->text( 'cutoff')->nullable();
			$table->text( 'definisi')->nullable();
        } );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('briguna', function (Blueprint $table) {
            $table->dropColumn([
                'uid','uid_pemrakarsa','tp_produk',
                'id_aplikasi','cif_las','Tgl_perkiraan_pensiun',
                'Sifat_suku_bunga','Briguna_profesi','Pendapatan_profesi',
                'Potongan_per_bulan','Plafond_briguna_existing',
                'Angsuran_briguna_existing','Suku_bunga','Jangka_waktu',
                'Baki_debet','Plafond_usulan','Rek_simpanan_bri',
                'Riwayat_pinjaman','Penguasaan_cashflow','Payroll',
                'Gaji_bersih_per_bulan','Maksimum_angsuran',
                'Tujuan_membuka_rek','Briguna_smart','Kode_fasilitas',
                'Tujuan_penggunaan_kredit','Penggunaan_kredit',
                'Provisi_kredit','Biaya_administrasi','Penalty',
                'Perusahaan_asuransi','Premi_asuransi_jiwa',
                'Premi_beban_bri','Premi_beban_debitur','Flag_promo',
                'Fid_promo','Pengadilan_terdekat','Bupln',
                'Agribisnis','Sandi_stp','Sifat_kredit',
                'Jenis_penggunaan','Sektor_ekonomi_sid','Sektor_ekonomi_lbu',
                'Jenis_kredit_lbu','Jenis_penggunaan','Jenis_penggunaan_lbu',
                'Sumber_aplikasi','Sifat_kredit_lbu','Kategori_kredit_lbu',
                'Jenis_penggunaan_lbu','id_Status_gelar',
                'cutoff','definisi','Status_gelar','score','grade'
            ]);
        });
    }
}
