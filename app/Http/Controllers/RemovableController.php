<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Database\Schema\Blueprint;
use Schema;

class RemovableController extends Controller
{
    public function run( Request $request ) {
        if( $request->header( 'password' ) == 'yudi.y@smooets.com' ) {
            $update_message = [];
            if( ! Schema::hasColumn( 'users', 'image' ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'image' )->nullable();
                } );
                $update_message[] = 'Add image field on user table!';
            }

            if( ! Schema::hasColumns( 'users', ['gender', 'phone', 'mobile_phone', 'is_actived'] ) ) {
                Schema::table( 'users', function ( Blueprint $table ) {
                    $table->string( 'phone' )->nullable();
                    $table->string( 'mobile_phone' )->nullable();
                    $table->enum( 'gender', ['L', 'P'] )->default('L');
                    $table->boolean( 'is_actived' )->default(true);
                } );
                $update_message[] = 'Add gender, phone, mobile_phone, is_actived fields on user table!';
            }

            if( Schema::hasColumns( 'customer_details', ['gender', 'phone', 'mobile_phone'] ) ) {
                Schema::table( 'customer_details', function ( Blueprint $table ) {
                    $table->dropColumn( ['gender', 'phone', 'mobile_phone'] );
                } );
                $update_message[] = 'Remove gender, phone, mobile_phone fields on customer_details table!';
            }

            if( ! Schema::hasColumn( 'customer_details', 'nik' ) ) {
                Schema::table( 'customer_details', function ( Blueprint $table ) {
                    $table->string( 'nik' )->nullable();
                } );
                $update_message[] = 'Add nik field on customer_details table!';
            }

            if( ! Schema::hasColumn( 'eforms', 'nik' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->string( 'nik' )->nullable();
                } );
                $update_message[] = 'Add nik field on eforms table!';
            }

            if( ! Schema::hasColumn( 'eforms', 'office_id' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->integer( 'office_id' )->nullable();
                    $table->foreign( 'office_id' )->references( 'id' )->on( 'offices' )->onUpdate( 'cascade' )->onDelete( 'cascade' );
                } );
                $update_message[] = 'Add office_id field on eforms table!';
            }

            if( Schema::hasColumn( 'eforms', 'branch' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->dropColumn( 'branch' )->nullable();
                } );
                $update_message[] = 'Remove branch field on eforms table!';
            }

            if( ! Schema::hasColumn( 'roles', 'is_default' ) ) {
                Schema::table( 'roles', function ( Blueprint $table ) {
                    $table->boolean('is_default')->default(false);
                } );
                $update_message[] = 'Add is_default field on roles table!';
            }

            if( ! Schema::hasColumn( 'offices', 'city_id' ) ) {
                Schema::table( 'offices', function ( Blueprint $table ) {
                    $table->integer('city_id')->unsigned()->nullable();

                    $table->foreign('city_id')->references('id')->on('cities')
                        ->onUpdate('cascade')
                        ->onDelete('set null');
                } );
                $update_message[] = 'Add city id field on offices table!';
            }

            if( Schema::hasColumns( 'users', ['email', 'first_name', 'last_name', 'phone', 'mobile_phone'] ) ) {

                Schema::table( 'users', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('users');
                    if (! $doctrinTable->hasIndex('users_email_index') ) {
                        $table->index('email');
                        $update_message[] = 'Add index for column email on table users!';
                    }

                    if (! $doctrinTable->hasIndex('users_first_name_index') ) {
                        $table->index('first_name');
                        $update_message[] = 'Add index for column first_name on table users!';
                    }

                    if (! $doctrinTable->hasIndex('users_last_name_index') ) {
                        $table->index('last_name');
                        $update_message[] = 'Add index for column last_name on table users!';
                    }

                    if (! $doctrinTable->hasIndex('users_phone_index') ) {
                        $table->index('phone');
                        $update_message[] = 'Add index for column phone on table users!';
                    }

                    if (! $doctrinTable->hasIndex('users_mobile_phone_index') ) {
                        $table->index('mobile_phone');
                        $update_message[] = 'Add index for column mobile_phone on table users!';
                    }
                } );
            }

            if( Schema::hasColumns( 'roles', ['name', 'slug'] ) ) {

                Schema::table( 'roles', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('roles');
                    if (! $doctrinTable->hasIndex('roles_name_index') ) {
                        $table->index('name');
                        $update_message[] = 'Add index for column name on table roles!';
                    }

                    if (! $doctrinTable->hasIndex('roles_slug_index') ) {
                        $table->index('slug');
                        $update_message[] = 'Add index for column slug on table roles!';
                    }
                } );
            }

            if( Schema::hasColumns( 'offices', ['name'] ) ) {

                Schema::table( 'offices', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('offices');
                    if (! $doctrinTable->hasIndex('offices_name_index') ) {
                        $table->index('name');
                        $update_message[] = 'Add index for column name on table offices!';
                    }
                } );
            }

            if( Schema::hasColumns( 'user_details', ['nip', 'position'] ) ) {

                Schema::table( 'user_details', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('user_details');
                    if (! $doctrinTable->hasIndex('user_details_nip_index') ) {
                        $table->index('nip');
                        $update_message[] = 'Add index for column nip on table user_details!';
                    }

                    if (! $doctrinTable->hasIndex('user_details_position_index') ) {
                        $table->index('position');
                        $update_message[] = 'Add index for column position on table user_details!';
                    }
                } );
            }

            if( Schema::hasColumns( 'cities', ['name'] ) ) {

                Schema::table( 'cities', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('cities');
                    if (! $doctrinTable->hasIndex('cities_name_index') ) {
                        $table->index('name');
                        $update_message[] = 'Add index for column name on table cities!';
                    }
                } );
            }

            if( Schema::hasColumns( 'developers', ['company_name'] ) ) {

                Schema::table( 'developers', function ( Blueprint $table ) use (&$update_message) {
                    $doctrinTable = Schema::getConnection()->getDoctrineSchemaManager()->listTableDetails('developers');
                    if (! $doctrinTable->hasIndex('developers_company_name_index') ) {
                        $table->index('company_name');
                        $update_message[] = 'Add index for column company_name on table developers!';
                    }
                } );
            }

            if( ! Schema::hasColumn( 'eforms', 'ref_number' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->string( 'ref_number' )->nullable();
                } );
                $update_message[] = 'Add ref_number field on eforms table!';
            }

            if(!Schema::hasColumns('developers', ['created_by', 'approved_by', 'is_approved', 'pks_number', 'plafond'])) {
                Schema::table('developers', function (Blueprint $table) {
                    $table->integer('created_by')->unsigned()->nullable();
                    $table->integer('approved_by')->unsigned()->nullable();
                    $table->boolean('is_approved')->default(false);
                    $table->string('pks_number')->nullable();
                    $table->string('plafond')->nullable();
                });
                $update_message[] = 'Add created_by, approved_by, is_approved, pks_number and plafond field on developers table!';
            }

            if(!Schema::hasColumns('properties', ['pic_name', 'pic_phone', 'is_approved', 'approved_by'])) {
                Schema::table('properties', function (Blueprint $table) {
                    $table->integer('approved_by')->unsigned()->nullable();
                    $table->string('pic_name')->nullable();
                    $table->string('pic_phone')->nullable();
                    $table->boolean('is_approved')->default(false);
                });
                $update_message[] = 'Add pic_name, pic_phone, is_approved and approved_by field on properties table!';
            }

            if( Schema::hasColumn( 'users', 'gender' ) ) {
                Schema::table( 'users', function( Blueprint $table ) {
                    $table->string( 'gender' )->nullable()->change();
                } );
                $update_message[] = 'Change gender field become nullable on users table!';
            }

            if (Schema::hasTable('developers_view_table') && ! Schema::hasColumn('developers_view_table', 'is_actived')) {
                \DB::unprepared("DROP VIEW IF EXISTS developers_view_table");
                \DB::unprepared("CREATE VIEW developers_view_table AS
                  SELECT 
                      users.id AS dev_id, 
                      developers.company_name AS company_name, 
                      concat(users.first_name, ' ', users.last_name) AS name,
                      users.email AS email,
                      users.phone AS phone_number,
                      users.is_actived AS is_actived,
                      cities.id AS city_id,
                      cities.name AS city_name,
                      ( 
                          SELECT count( * ) FROM properties
                              LEFT JOIN property_types ON properties.id = property_types.property_id
                              LEFT JOIN property_items ON property_types.id = property_items.property_type_id
                              where developers.id = properties.developer_id
                      ) AS project 

                  FROM developers
                      inner JOIN users ON users.id = developers.user_id
                      inner JOIN cities ON cities.id = developers.city_id
                ");

                $update_message[] = 'Update developers_view_table';
            }

            if( ! Schema::hasColumn( 'eforms', 'product_type' ) ) {
                Schema::table( 'eforms', function ( Blueprint $table ) {
                    $table->dropColumn( 'product' );
                    $table->string( 'product_type' )->nullable();
                } );
                $update_message[] = 'Add product_type field and remove product field on eforms table!';
            }

            if( ! Schema::hasColumns( 'customer_details', [ 'couple_nik', 'couple_name', 'couple_birth_place', 'couple_birth_date', 'couple_identity' ] ) ) {
                Schema::table( 'customer_details', function( Blueprint $table ) {
                        $table->string( 'couple_nik' )->nullable();
                        $table->string( 'couple_name' )->nullable();
                        $table->string( 'couple_birth_place' )->nullable();
                        $table->date( 'couple_birth_date' )->nullable();
                        $table->string( 'couple_identity' )->nullable();
                    } );
                $update_message[] = 'Add couple_nik, couple_name, couple_birth_place, couple_birth_date, couple_identity, field on customer_details table!';
            }

            if( ! Schema::hasColumn( 'properties', 'slug' ) ) {
                Schema::table( 'properties', function( Blueprint $table ) {
                        $table->string( 'slug' )->nullable();
                    } );
                $update_message[] = 'Add slug field on properties table!';
            }

            if (Schema::hasTable('developer_properties_view_table') && ! Schema::hasColumn('developer_properties_view_table', 'slug')) {
                \DB::unprepared("DROP VIEW IF EXISTS developer_properties_view_table");
                \DB::unprepared("CREATE VIEW developer_properties_view_table AS
                    SELECT 
                        properties.id AS prop_id,
                        properties.name AS prop_name,
                        properties.pic_name AS prop_pic_name,
                        properties.pic_phone AS prop_pic_phone,
                        properties.slug AS prop_slug,
                        properties.city_id AS prop_city_id,
                        properties.category AS prop_category,
                        cities.name AS prop_city_name,
                        ( SELECT developers.user_id FROM developers WHERE properties.developer_id = developers.id ) AS prop_dev_id,
                        ( SELECT count(property_types.id) FROM property_types WHERE properties.id = property_types.property_id) AS prop_types,
                        ( SELECT count(property_items.id) FROM property_items inner join property_types on property_types.id = property_items.property_type_id where properties.id = property_types.property_id) AS prop_items
                    FROM properties
                        INNER JOIN cities ON properties.city_id = cities.id
                ");

                $update_message[] = 'Update developer_properties_view_table';
            }

            if( ! Schema::hasColumn( 'property_types', 'slug' ) ) {
                Schema::table( 'property_types', function( Blueprint $table ) {
                    $table->string( 'slug' )->nullable();
                } );
                $update_message[] = 'Add slug field on property types table!';
            }

            if( Schema::hasColumn( 'properties', 'category' ) ) {
                Schema::table( 'properties', function( Blueprint $table ) {
                    $table->string( 'category' )->change();
                } );
                $update_message[] = 'Add category field on property table!';
            }

            if( empty( $update_message ) ) {
                return response()->json( [
                    'message' => 'No update'
                ], 200 );
            } else {
                return response()->json( [
                    'message' => $update_message
                ], 200 );
            }
        } else {
            return response()->json( [
                'message' => 'Not authorized!'
            ], 400 );
        }
    }
}
