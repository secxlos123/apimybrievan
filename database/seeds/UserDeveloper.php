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
                'id' => 1,
                'email' => 'mybri@bri.co.id',
                'password' => bcrypt('12345678'),
                'first_name' => 'Non Kerja Sama',
                'last_name' => 'BRI',
            ]);

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);


            $developer = $user->developer()->create([
                'id' => 1,
                'dev_id_bri' => 1,
                'company_name' => 'Non Kerja Sama',
                'created_by' => 'BRI',
                'pks_number' => '-',
                'address' => '-',
                'summary' => '-',
                'plafond' => '-'
            ]);

            $developer->properties()->create([
                'id' => 1,
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
                'id' => 2,
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

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);            

            $developer = $user->developer()->create([
                "id" => 2,
                "user_id" => 2,
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
                "id" => 3,
                'email' => 'artha.putra.prima@mailinator.com',
                'password' => bcrypt('12345678'),
                "image" => "1511262257.jpg",
                "phone" => "021000001",
                "mobile_phone" => "087809904499",
                "gender" => "L",
                "is_actived" => "1"
            ]);

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            $developer = $user->developer()->create([
                "id" => 3,
                "user_id" => 3,
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

            $property = $developer->properties()->create([
                'id' => 2,
                'developer_id' => 3,
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

            $p1 = $property->propertyTypes()->create([
                'id' => 1,
                'property_id' => 2,
                'name' => 'Akasia Terrace',
                'surface_area' => 145,
                'building_area' => 69,
                'price' => 1600000000,
                'electrical_power' => '2200',
                'bathroom' => 1,
                'bedroom' => 3,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-terrace-akasia-terrace',
            ]);

            $p1->propertyItems()->create([
                'id' => 1,
                'property_type_id' => 1,
                'address' => 'Blok 1',
                'price' => 1600000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p2 = $property->propertyTypes()->create([
                'id' => 2,
                'property_id' => 2,
                'name' => 'Akasia Terrace',
                'surface_area' => 120,
                'building_area' => 69,
                'price' => 1350000000,
                'electrical_power' => '2200',
                'bathroom' => 1,
                'bedroom' => 3,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-terrace-akasia-terrace-1',
            ]);

            $p2->propertyItems()->create([
                'id' => 2,
                'property_type_id' => 2,
                'address' => 'Blok II',
                'price' => 1350000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $poperty = $developer->properties()->create([
                'id' => 3,
                'developer_id' => 3,
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

            $p1 = $property->propertyTypes()->create([
                'id' => 3,
                'property_id' => 3,
                'name' => 'Akasia Serenity 1',
                'surface_area' => 74,
                'building_area' => 63,
                'price' => 1455000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 3,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-1',
            ]);

            $p1->propertyItems()->create([
                'id' => 3,
                'property_type_id' => 3,
                'address' => 'Blok 1',
                'price' => 1455000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p2 = $property->propertyTypes()->create([
                'id' => 4,
                'property_id' => 3,
                'name' => 'Akasia Serenity 2',
                'surface_area' => 122,
                'building_area' => 69,
                'price' => 1670000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-2',
            ]);

            $p2->propertyItems()->create([
                'id' => 4,
                'property_type_id' => 4,
                'address' => 'Blok 2',
                'price' => 1670000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p3 = $property->propertyTypes()->create([
                'id' => 5,
                'property_id' => 3,
                'name' => 'Akasia Serenity 3',
                'surface_area' => 78,
                'building_area' => 69,
                'price' => 1583350000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-3',
            ]);

            $p3->propertyItems()->create([
                'id' => 5,
                'property_type_id' => 5,
                'address' => 'Blok 3',
                'price' => 1583350000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p4 = $property->propertyTypes()->create([
                'id' => 6,
                'property_id' => 3,
                'name' => 'Akasia Serenity 4',
                'surface_area' => 97,
                'building_area' => 76,
                'price' => 1690000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-iv',
            ]);

            $p4->propertyItems()->create([
                'id' => 6,
                'property_type_id' => 6,
                'address' => 'Blok 4',
                'price' => 1690000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p5 = $property->propertyTypes()->create([
                'id' => 7,
                'property_id' => 3,
                'name' => 'Akasia Serenity 5',
                'surface_area' => 86,
                'building_area' => 63,
                'price' => 1500000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-5',
            ]);

            $p5->propertyItems()->create([
                'id' => 7,
                'property_type_id' => 7,
                'address' => 'Blok 5',
                'price' => 1500000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p6 = $property->propertyTypes()->create([
                'id' => 8,
                'property_id' => 3,
                'name' => 'Akasia Serenity 6',
                'surface_area' => 103,
                'building_area' => 69,
                'price' => 1526000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-6',
            ]);

            $p6->propertyItems()->create([
                'id' => 8,
                'property_type_id' => 8,
                'address' => 'Blok 6',
                'price' => 1526000000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p7 = $property->propertyTypes()->create([
                'id' => 9,
                'property_id' => 3,
                'name' => 'Akasia Serenity 7',
                'surface_area' => 96,
                'building_area' => 79,
                'price' => 1680000000,
                'electrical_power' => '2200',
                'bathroom' => 2,
                'bedroom' => 4,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'akasia-serenity-akasia-serenity-7',
            ]);

            $p7->propertyItems()->create([
                'id' => 9,
                'property_type_id' => 9,
                'address' => 'Blok 7',
                'price' => 1680000000,
                'status' => 'new',
                'is_available' => true,
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
                "id" => 4,
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

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            $developer = $user->developer()->create([
                "id" => 4,
                "user_id" => 4,
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

            $property = $developer->properties()->create([
                'id' => 4,
                'developer_id' => 4,
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

            $p1 = $property->propertyTypes()->create([
                'id' => 10,
                'property_id' => 4,
                'name' => 'Jaya Imperial I',
                'surface_area' => 60,
                'building_area' => 30,
                'price' => 348150000,
                'electrical_power' => '2200',
                'bathroom' => 1,
                'bedroom' => 2,
                'floors' => 1,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'jaya-imperial-jaya-imperial-i',
            ]);

            $p1->propertyItems()->create([
                'id' => 10,
                'property_type_id' => 10,
                'address' => 'Blok 1',
                'price' => 348150000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p2 = $property->propertyTypes()->create([
                'id' => 11,
                'property_id' => 4,
                'name' => 'Jaya Imperial II',
                'surface_area' => 94,
                'building_area' => 30,
                'price' => 465106125,
                'electrical_power' => '2200',
                'bathroom' => 1,
                'bedroom' => 2,
                'floors' => 1,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'jaya-imperial-jaya-imperial-ii',
            ]);

            $p2->propertyItems()->create([
                'id' => 11,
                'property_type_id' => 11,
                'address' => 'Blok II',
                'price' => 465106125,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p3 = $property->propertyTypes()->create([
                'id' => 12,
                'property_id' => 4,
                'name' => 'Jaya Imperial III',
                'surface_area' => 97,
                'building_area' => 30,
                'price' => 474999938,
                'electrical_power' => '2200',
                'bathroom' => 1,
                'bedroom' => 2,
                'floors' => 1,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'jaya-imperial-jaya-imperial-iii',
            ]);

            $p3->propertyItems()->create([
                'id' => 12,
                'property_type_id' => 12,
                'address' => 'Blok III',
                'price' => 474999938,
                'status' => 'new',
                'is_available' => true,
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
                "id" => 5,
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

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            $developer = $user->developer()->create([
                "id" => 5,
                "user_id" => 5,
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

            $property = $developer->properties()->create([
                'id' => 5,
                'developer_id' => 5,
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

            $p1 = $property->propertyTypes()->create([
                'id' => 13,
                'property_id' => 5,
                'name' => 'Talaga Bestari (Ruko) I',
                'surface_area' => 102,
                'building_area' => 53,
                'price' => 999606000,
                'electrical_power' => '4400',
                'bathroom' => 1,
                'bedroom' => 1,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'talaga-bestari-ruko-talaga-bestari-ruko-i',
            ]);

            $p1->propertyItems()->create([
                'id' => 13,
                'property_type_id' => 13,
                'address' => 'Ruko I',
                'price' => 999606000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p2 = $property->propertyTypes()->create([
                'id' => 14,
                'property_id' => 5,
                'name' => 'Telaga Bestari (Ruko) II',
                'surface_area' => 121,
                'building_area' => 62,
                'price' => 1128483000,
                'electrical_power' => '4400',
                'bathroom' => 1,
                'bedroom' => 1,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'talaga-bestari-ruko-telaga-bestari-ruko-ii',
            ]);

            $p2->propertyItems()->create([
                'id' => 14,
                'property_type_id' => 14,
                'address' => 'Ruko II',
                'price' => 1128483000,
                'status' => 'new',
                'is_available' => true,
            ]);

            $p3 = $property->propertyTypes()->create([
                'id' => 15,
                'property_id' => 5,
                'name' => 'Telaga Bestari (Ruko) III',
                'surface_area' => 40,
                'building_area' => 78,
                'price' => 808464000,
                'electrical_power' => '4400',
                'bathroom' => 1,
                'bedroom' => 1,
                'floors' => 2,
                'carport' => 0,
                'certificate' => 'SHM',
                'slug' => 'talaga-bestari-ruko-telaga-bestari-ruko-iii',
            ]);

            $p3->propertyItems()->create([
                'id' => 15,
                'property_type_id' => 15,
                'address' => 'Ruko III',
                'price' => 808464000,
                'status' => 'new',
                'is_available' => true,
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
                "id" => 6,
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

            $user->roles()->attach(4);
            $activation = Activation::create($user);
            Activation::complete($user, $activation->code);

            $developer = $user->developer()->create([
                "id" => 6,
                "user_id" => 6,
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