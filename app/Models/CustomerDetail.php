<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use File;

class CustomerDetail extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'customer_details';

    /**
     * Disabling timestamp feature.
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'nik', 'birth_place' , 'birth_date', 'address', 'gender', 'city', 'phone', 'citizenship', 'status', 'address_status', 'mother_name', 'mobile_phone', 'emergency_contact', 'emergency_relation', 'identity', 'npwp', 'work_type', 'work', 'company_name', 'work_field', 'position', 'work_duration', 'office_address', 'salary', 'other_salary', 'loan_installment', 'dependent_amount'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'user_id'
    ];

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getNpwpAttribute( $value )
    {
        if( File::exists( asset( 'uploads/users/' . $this->id . '/' . $value ) ) ) {
            $image = url( asset( 'uploads/users/' . $this->id . '/' . $value ) );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }

    /**
     * Get user avatar image url.
     *
     * @return string
     */
    public function getIdentityAttribute( $value )
    {
        if( File::exists( asset( 'uploads/users/' . $this->id . '/' . $value ) ) ) {
            $image = url( asset( 'uploads/users/' . $this->id . '/' . $value ) );
        } else {
            $image = url( 'img/noimage.jpg' );
        }
        return $image;
    }
}
