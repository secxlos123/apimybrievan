<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\VisitReportMutation;

class VisitReport extends Model
{
    /**
     * The table name.
     *
     * @var string
     */
    protected $table = 'visit_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'eform_id', 'visitor_name', 'place', 'date', 'name', 'job', 'phone', 'account', 'amount', 'type', 'purpose_of_visit', 'result', 'source', 'income', 'income_salary', 'income_allowance', 'income_mutation_type', 'income_mutation_number', 'income_salary_image', 'business_income', 'business_mutation_type', 'bussiness_mutation_number', 'bussiness_other', 'mutation_bank', 'mutation_number', 'photo_with_customer', 'pros', 'cons', 'seller_name', 'seller_address', 'seller_phone', 'selling_price', 'reason_for_sale', 'relation_with_seller' ];

    /**
     * Set photo with customer.
     *
     * @return void
     */
    public function setPhotoWithCustomerAttribute( $image )
    {
        $path = public_path( 'uploads/eforms/' . $this->eform_id . '/visit_report/' );
        if ( ! empty( $this->attributes[ 'photo_with_customer' ] ) ) {
            File::delete( $path . $this->attributes[ 'photo_with_customer' ] );
        }

        $filename = time() . '.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'photo_with_customer' ] = $filename;
    }

    /**
     * Set visit report mutation information.
     *
     * @return void
     */
    public function setMutations( $mutations ) {
        foreach ( $mutations as $key => $mutation ) {
            VisitReportMutation::create( [ 'visit_report_id' => $this->id ] + $mutation );
        }
    }

    /**
     * The relation to visit report mutations.
     *
     * @return     \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mutations()
    {
        return $this->hasMany( VisitReportMutation::class, 'visit_report_id' );
    }
}