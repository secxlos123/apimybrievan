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
        $skiptab = ['role_users', 'jenis_pinjaman', 'tujuan_penggunaan', 'tbl_kodepos', 'pendidikan_terakhir', 'mitra_relation', 'mitra', 'notifications', 'gimmick_sukubunga', 'gimmick_kredit', 'gimmick_pemutus', 'gimmick','view_label', 'view_div', 'view_table','dirrpc_detail','gimmick_detail','dirrpc','dirrpc_detail','mitra2','export_table','mitra_sebelum','mitra_before','mitra_baru','mitra3','uker_tables'];
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
