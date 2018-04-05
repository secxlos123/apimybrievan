<?php

use Illuminate\Database\Seeder;

class FixAutoId extends Seeder
{
    /**
     * Fix AutoIncrement ID.
     * @author Akse
     * @return void
     */
    public function run()
    {
        $table = \DB::select('SELECT * FROM information_schema.tables WHERE table_schema=? AND table_type=?', array('public', 'BASE TABLE'));
        $skiptab = ['role_users', 'jenis_pinjaman', 'tujuan_penggunaan', 'tbl_kodepos', 'pendidikan_terakhir', 'mitra_relation', 'mitra', 'notifications', 'gimmick_sukubunga', 'gimmick_kredit', 'gimmick_pemutus', 'gimmick','view_label', 'view_div', 'view_table','dirrpc_detail','gimmick_detail','dirrpc','dirrpc_detail','mitra2','export_table','mitra_sebelum','mitra_before','mitra_baru','mitra3','uker_tables','ddkoperasi_tingkat2','ddkoperasi_tingkat3','ddlainnya','ddlainnya_tingkat2','ddlainnya_tingkat3','ddnegara','ddnegara_tingkat2','ddnegara_tingkat3','mitras','mitra_copy_copy_copy',
            'ddpensiunan_asabri','ddpensiunan_asabri_tingkat2','ddpensiunan_asabri_tingkat3',
            'ddpensiunan_bri','ddpensiunan_bri_tingkat2','ddpensiunan_bri_tingkat3','ddpensiunan_bumn',
            'ddpensiunan_bumn_tingkat2','ddpensiunan_bumn_tingkat3','ddpensiunan_lainnya',
            'ddpensiunan_lainnya_tingkat2','ddpensiunan_lainnya_tingkat3','ddpensiunan_taspen',
            'ddpensiunan_taspen_tingkat2','ddpensiunan_taspen_tingkat3','ddpolri','ddpolri_tingkat2',
            'ddpolri_tingkat3','ddswasta','ddswasta_tingkat2','ddswasta_tingkat3','ddtni',
            'ddtni_tingkat2','ddtni_tingkat3','ddyayasan','ddyayasan_tingkat2','ddyayasan_tingkat3',
            'ddbank','ddbank','ddbumd','ddbumd_tingkat2','ddbumd_tingkat3','ddbumn','ddbumn_tingkat2',
            'ddbumn_tingkat3','ddfasilitas','ddkementrian','ddkementrian_tingkat2',
            'ddkementrian_tingkat3','ddkoperasi','mitra_detail','mitra_detail_dasar',
            'mitra_detail_data','log_mitra','mitra_create','mitra_detail_fasilitas',
            'mitra_detail_fasilitas_perbankan','mitra_utama','mitra_pemutus',
            'mitra_penilaian_kelayakan','mitra_perjanjian'];
        foreach ($table as $key => $value) {
            $id = $value->table_name . '_id_seq';
            $tab = $value->table_name;
            if (!in_array($tab, $skiptab)) {
                \DB::statement("SELECT MAX(id) FROM $tab ");
                \DB::statement("SELECT nextval('$id')");
                \DB::statement("BEGIN");
                \DB::statement("LOCK TABLE $tab IN EXCLUSIVE MODE");
                \DB::statement("SELECT setval( '$id', COALESCE((SELECT MAX(id)+1 FROM $tab), 1),false)");
                \DB::statement("COMMIT");
            } else {
                continue;
            }
        }

    }
}
