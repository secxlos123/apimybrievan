<?php

use Illuminate\Database\Seeder;

class UserDeveloper extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::beginTransaction();
        try {
            $this->createNonPKS();
            $this->createAlamkaryaSelaras();
            $this->createArthaPutraPrima();
            $this->createJayaGardenPolis();
            $this->createSinarPuspapersada();
            $this->createCitraMajaJO();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            dd($e->getMessage());
        }
    }

    /**
     * Create Non PKS Developer
     *
     * @return void
     **/
    public function createNonPKS()
    {
        if ( ! $user = App\Models\User::findEmail('mybri@bri.co.id') ) {
            $user = App\Models\User::create([
                'id' => 101,
                'email' => 'mybri@bri.co.id',
                'password' => bcrypt('12345678'),
                'first_name' => 'Non Kerja Sama',
                'last_name' => 'BRI',
            ]);

            $developer = $user->developer()->create([
                'id' => 101,
                'dev_id_bri' => 1,
                'company_name' => 'Non Kerja Sama',
                'created_by' => 'BRI',
                'pks_number' => '-',
                'address' => '-',
                'summary' => '-',
                'plafond' => '-'
            ]);

            $developer->properties()->create([
                'id' => 101,
                'name' => 'Non Kerja Sama',
                'prop_id_bri' => 1,
                'description' => '-',
                'facilities'  => '-',
                'pic_phone' => '-',
                'address'   => '-',
                'category'  => 3,
                'latitude'  => '0',
                'longitude' => '0',
                'pic_name'  => 'BRI'
            ]);
        }
    }

    /**
     * Create Alamkarya Selaras Developer
     *
     * @return void
     **/
    public function createAlamkaryaSelaras()
    {
        if ( ! $user = App\Models\User::findEmail('alamkarya.selaras@mailinator.com') ) {
            $user = App\Models\User::create([
                'id' => 102,
                'email' => 'alamkarya.selaras@mailinator.com',
                'password' => bcrypt('12345678'),
                'first_name' => 'Prima',
                'last_name' => 'Setya',
                'image' => '1510947970.jpeg',
                'phone' => '081388691575',
                'mobile_phone' => '081388691575',
                'gender' => 'L',
                'is_actived' => '1'
            ]);

            $developer = $user->developer()->create([
                "id" => 102,
                "user_id" => 102,
                "city_id" => 455,
                "dev_id_bri" => '',
                "created_by" => "00073042",
                "approved_by" => "",
                "company_name" => "PT ALAMKARYA SELARAS",
                "pks_number" => "-",
                "plafond" => "0",
                "address" => "PT ALAMKARYA SELARAS",
                "summary" => "wess",
                "is_approved" => "0",
                "pks_description" => ''
            ]);
        }
    }


    /**
     * Create Artha Putra Prima Developer
     *
     * @return void
     **/
    public function createArthaPutraPrima()
    {
        if ( ! $user = App\Models\User::findEmail('artha.putra.prima@mailinator.com') ) {
            $user = App\Models\User::create([
                "id" => 103,
                'email' => 'artha.putra.prima@mailinator.com',
                'password' => bcrypt('12345678'),
                "image" => "1511262257.jpg",
                "phone" => "021000001",
                "mobile_phone" => "087809904499",
                "gender" => "L",
                "is_actived" => "1"
            ]);

            $developer = $user->developer()->create([
                "id" => 103,
                "user_id" => 103,
                "city_id" => 457,
                "created_by" => "00066777",
                "approved_by" => "",
                "company_name" => "PT. Artha Putra Prima",
                "pks_number" => "-",
                "plafond" => "0",
                "address" => "PT. Artha Putra Prima",
                "summary" => "Developer di daerah Tangerang Selatan",
                "is_approved" => "0",
                "pks_description" => ''
            ]);

            $developer->properties()->create([
                'id' => 103,
                'developer_id' => 103,
                'city_id' => 457,
                'name' => 'Akasia Terrace',
                'pic_name' => 'Tantan',
                'pic_phone' => '087809904499',
                'address' => 'Jl. Raya Pd. Petir, Pd. Petir, Bojongsari, Kota Depok, Jawa Barat 16517',
                'category' => '0',
                'latitude' => '-6.90390',
                'longitude' => '107.61860',
                'description' => '<p>Deskripsi Property</p>',
                'facilities' => '<p>CCTV, Playground</p>',
                'slug' => 'pt-artha-putra-prima-akasia-terrace',
                'is_approved' => false,
                'pks_number' => '123456789',
                'status' => 'new',
            ]);

            $developer->properties()->create([
                'id' => 104,
                'developer_id' => 103,
                'city_id' => 457,
                'name' => 'Akasia Serenity',
                'pic_name' => 'Tantan',
                'pic_phone' => '087809904499',
                'address' => 'Jl. Sumatera, Jombang, Ciputat, Kota Tangerang Selatan, Banten 15414',
                'category' => '0',
                'latitude' => '-6.90390',
                'longitude' => '107.61860',
                'description' => '<p>Deskripsi Property</p>',
                'facilities' => '<p>CCTV, Playground</p>',
                'slug' => 'pt-artha-putra-prima-akasia-serenity',
                'is_approved' => false,
                'pks_number' => '1234456777',
                'status' => 'new'
            ]);
        }
    }

    /**
     * Create Jaya Garden Polis Developer
     *
     * @return void
     **/
    public function createJayaGardenPolis()
    {
        if ( ! $user = App\Models\User::findEmail('jaya.garden.polis@mailinator.com') ) {
            $user = App\Models\User::create([
                "id" => 104,
                "password" => bcrypt('12345678'),
                "email" => "jaya.garden.polis@mailinator.com",
                "first_name" => "Sapto",
                "last_name" => "",
                "image" => "1511262275.jpg",
                "phone" => "021000002",
                "mobile_phone" => "082125093309",
                "gender" => "L",
                "is_actived" => "1",
            ]);

            $developer = $user->developer()->create([
                "id" => 104,
                "user_id" => 104,
                "city_id" => 457,
                "created_by" => "00066777",
                "company_name" => "PT. Jaya Garden Polis",
                "pks_number" => "-",
                "plafond" => "0",
                "address" => "PT. Jaya Garden Polis",
                "summary" => "Developer di daerah Tangerang Selatan",
                "is_approved" => "0",
                "pks_description" => ''
            ]);

            $developer->properties()->create([
                'id' => 105,
                'developer_id' => 104,
                'city_id' => 455,
                'name' => 'Jaya Imperial',
                'pic_name' => 'Sapto',
                'pic_phone' => '082125093309',
                'address' => 'Sepatan, Tangerang, Banten 15520',
                'category' => '0',
                'latitude' => '-6.90390',
                'longitude' => '107.61860',
                'description' => '<p>Deskripsi Property</p>',
                'facilities' => '<p>CCTV, Playground</p>',
                'slug' => 'pt-jaya-garden-polis-jaya-imperial',
                'is_approved' => false,
                'pks_number' => '12345678',
                'status' => 'new',
            ]);
        }
    }


    /**
     * Create Sinar Puspapersada Developer
     *
     * @return void
     **/
    public function createSinarPuspapersada()
    {
        if ( ! $user = App\Models\User::findEmail('sinar.puspapersada@mailinator.com') ) {
            $user = App\Models\User::create([
                "id" => 105,
                "password" => bcrypt('12345678'),
                "email" => "sinar.puspapersada@mailinator.com",
                "first_name" => "Devy",
                "last_name" => "Oktaviani",
                "image" => "1511262243.jpg",
                "phone" => "021000003",
                "mobile_phone" => "085222872267",
                "gender" => "L",
                "is_actived" => "1",
            ]);

            $developer = $user->developer()->create([
                "id" => 105,
                "user_id" => 105,
                "city_id" => 457,
                "created_by" => "00066777",
                "approved_by" => "",
                "company_name" => "PT. Sinar Puspapersada",
                "pks_number" => "-",
                "plafond" => "0",
                "address" => "PT. Sinar Puspapersada",
                "summary" => "Developer di daerah Tangerang Selatan",
                "is_approved" => "0",
                "pks_description" => ''
            ]);

            $developer->properties()->create([
                'id' => 106,
                'developer_id' => 105,
                'city_id' => 455,
                'name' => 'Talaga Bestari (Ruko)',
                'pic_name' => 'Devy Oktaviani',
                'pic_phone' => '085222872267',
                'address' => 'Wana Kerta, Sindang Jaya, Tangerang, Banten 15560',
                'category' => '1',
                'latitude' => '-6.90390',
                'longitude' => '107.61860',
                'description' => '<p>Deskripsi Property</p>',
                'facilities' => '<p>Playground</p>',
                'slug' => 'pt-sinar-puspapersada-talaga-bestari-ruko',
                'is_approved' => false,
                'pks_number' => '12345678',
                'status' => 'new',
            ]);
        }
    }


    /**
     * Create Citra Maja JO Developer
     *
     * @return void
     **/
    public function createCitraMajaJO()
    {
        if ( ! $user = App\Models\User::findEmail('citra.maja.jo@mailinator.com') ) {
            $user = App\Models\User::create([
                "id" => 106,
                "password" => bcrypt('12345678'),
                "email" => "citra.maja.jo@mailinator.com",
                "first_name" => "Parmin",
                "last_name" => "",
                "image" => "1511263477.jpg",
                "phone" => "021000004",
                "mobile_phone" => "082114690033",
                "gender" => "L",
                "is_actived" => "1",
            ]);

            $developer = $user->developer()->create([
                "id" => 106,
                "user_id" => 106,
                "city_id" => 457,
                "dev_id_bri" => "1057",
                "created_by" => "00073042",
                "company_name" => "PT. CITRA MAJA JO",
                "pks_number" => "-",
                "plafond" => "0",
                "address" => "42178",
                "summary" => "developer",
                "is_approved" => "0",
                "created_at" => "2017-11-21 11:24:37",
                "updated_at" => "2017-11-21 11:24:37",
                "pks_description" => ''
            ]);
        }
    }
}