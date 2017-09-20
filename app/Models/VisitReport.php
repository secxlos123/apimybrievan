<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\BankStatement;
use App\Models\Mutation;

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
    protected $fillable = [ 'eform_id', 'visitor_name', 'place', 'date', 'name', 'job', 'phone', 'account', 'amount', 'type', 'purpose_of_visit', 'result', 'source', 'income', 'income_salary', 'income_allowance', 'income_mutation_type', 'income_mutation_number', 'income_salary_image', 'business_income', 'business_mutation_type', 'bussiness_mutation_number', 'bussiness_other', 'mutation_file', 'photo_with_customer', 'pros', 'cons', 'seller_name', 'seller_address', 'seller_phone', 'selling_price', 'reason_for_sale', 'relation_with_seller' ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public static function create( $data ) {
        $visit_report = ( new static )->newQuery()->create( $data );
        foreach ( $data[ 'mutations' ] as $key => $mutation_data ) {
            $mutation = Mutation::create( [
                'visit_report_id' => $visit_report->id
            ] + $mutation_data );
            foreach ( $mutation_data[ 'tables' ] as $key => $bank_statement_data ) {
                BankStatement::create( [
                    'mutation_id' => $mutation->id
                ] + $bank_statement_data );
            }
        }
    }

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

        $filename = time() . '-photo-with-customer.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'photo_with_customer' ] = $filename;
    }

    /**
     * Set photo with customer.
     *
     * @return void
     */
    public function setIncomeSalaryImageAttribute( $image )
    {
        $path = public_path( 'uploads/eforms/' . $this->eform_id . '/visit_report/' );
        if ( ! empty( $this->attributes[ 'income_salary_image' ] ) ) {
            File::delete( $path . $this->attributes[ 'income_salary_image' ] );
        }

        $filename = time() . '-income-salary-image.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'income_salary_image' ] = $filename;
    }

    /**
     * Set mutation file upload.
     *
     * @return void
     */
    public function setMutationFileAttribute( $image )
    {
        $path = public_path( 'uploads/eforms/' . $this->eform_id . '/visit_report/' );
        if ( ! empty( $this->attributes[ 'mutation_file' ] ) ) {
            File::delete( $path . $this->attributes[ 'mutation_file' ] );
        }

        $filename = time() . '-mutations.' . $image->getClientOriginalExtension();
        $image->move( $path, $filename );
        $this->attributes[ 'mutation_file' ] = $filename;
    }
}