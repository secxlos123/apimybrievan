<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lang;
use DB;
use App\Models\ApprovalDataChange;

class UserNotification extends Model
{
	protected $table = 'notifications';
	/**
	 * The attributes that should be casted to native types.
	 *
	 * @var array
	 */
	public $incrementing = false;

	protected $casts = [
		'id' => 'string',
		'data' => 'array',
		'type' => 'string',
		'type_module' => 'string',
	];

	public function scopeUnreads( $query )
	{
		return $query->whereNull('read_at');
	}

	public function scopePinca( $query, $branch_id )
	{
		return $query->leftJoin( 'eforms', 'notifications.slug', '=', 'eforms.id' )
			->where( 'notifications.branch_id', $branch_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\PengajuanKprNotification'
					, 'App\Notifications\PencairanInternal'
					, 'App\Notifications\ApproveEFormCLAS'
					, 'App\Notifications\RejectEFormCLAS'
					, 'App\Notifications\LKNEFormCustomer'
					, 'App\Notifications\LKNEFormRecontest'
				)
			);
	}

	public function scopeAo( $query, $pn )
	{
		return $query->leftJoin( 'eforms', 'notifications.slug', '=', 'eforms.id' )
			->leftJoin( 'collaterals', function ( $join ) {
                $join->on( 'collaterals.id', '=' , 'notifications.slug' )
                    ->where( function( $q ) {
						$q->whereIn( 'notifications.type_module'
							, array(
								'collateral'
								, 'collateral_manager_approving'
							)
						);
		            } )
                    ->where( function( $q ) {
						$q->whereIn( 'notifications.type'
							, array(
								'App\Notifications\CollateraAODisposition'
								, 'App\Notifications\CollateralManagerApproveAO'
								, 'App\Notifications\CollateralManagerRejectedAO'
							)
						);
		            } );
            } )
            ->where( function( $q ) use ( $pn ) {
				$q->where( 'eforms.ao_id', $pn )
				    ->orWhereNotNull( 'collaterals.id' );
            } )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\EFormPenugasanDisposisi'
					, 'App\Notifications\ApproveEFormInternal'
					, 'App\Notifications\RejectEFormInternal'
					, 'App\Notifications\PencairanInternal'
					, 'App\Notifications\ApproveEFormCLAS'
					, 'App\Notifications\RejectEFormCLAS'
					, 'App\Notifications\ScorePefindoPreScreening'
					, 'App\Notifications\VerificationApproveFormNasabah'
					, 'App\Notifications\VerificationRejectFormNasabah'
					, 'App\Notifications\CollateraAODisposition'
					, 'App\Notifications\CollateralManagerRejectedAO'
					, 'App\Notifications\CollateralManagerApproveAO'
					, 'App\Notifications\RecontestEFormNotification'
				)
			);
	}

	public function scopeCustomer( $query, $user_id )
	{
		return $query->where( 'notifications.notifiable_id', $user_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\ApproveEFormCLASCustomer'
					, 'App\Notifications\RejectEFormCustomer'
					, 'App\Notifications\RejectEFormCLASCustomer'
					, 'App\Notifications\PencairanNasabah'
					, 'App\Notifications\VerificationDataNasabah'
					, 'App\Notifications\NewSchedulerCustomer'
					, 'App\Notifications\UpdateSchedulerCustomer'
				)
			);
	}

	public function scopeStaff( $query, $blank )
	{
		return $query->whereNull('notifications.created_at');

	}

	public function scopeCollateralAppraisal( $query, $branch_id )
	{
		return $query->where( 'notifications.branch_id', $branch_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\CollateralDisposition'
					, 'App\Notifications\CollateralManagerRejected'
					, 'App\Notifications\CollateralManagerApprove'
				)
			);

	}

	public function scopeCollateral( $query, $branch_id )
	{
    	return $query->where( 'notifications.branch_id', $branch_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\PropertyNotification'
					, 'App\Notifications\CollateralOTS'
					, 'App\Notifications\CollateralStafRejectOTS'
					, 'App\Notifications\CollateralStafPenilaianAnggunan'
					, 'App\Notifications\CollateralStafChecklist'
				)
			);
	}

	public function scopeDeveloper( $query, $user_id )
	{
		return $query->where( 'notifications.notifiable_id', $user_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\EditDeveloper'
					, 'App\Notifications\ApproveDeveloperProfile'
					, 'App\Notifications\RejectDeveloperProfile'
					, 'App\Notifications\CollateralManagerRejected'
					, 'App\Notifications\CollateralManagerApprove'
				)
			);

	}

	public function notifiable()
	{
		return $this->morphTo();
	}

	/**
	 * Mark the notification as read.
	 *
	 * @return void
	 */
	public function markAsRead( $is_read = null )
	{
		if ( is_null( $this->read_at ) ) {
			$this->forceFill(
				array(
					'read_at' => $this->freshTimestamp()
					, 'is_read' => $is_read
				)
			)->save();
		}
	}

	public function getIsReadAttribute()
	{
		return (bool) $this->read_at;
	}

	public function getSubject($status_eform, $ref_number, $user_id)
	{
		return $this->defineSubject(
			$this->defineUrl( $ref_number, $user_id )
			, $status_eform
			, getTypeModule(Collateral::class)
			, ApprovalDataChange::where( 'related_id', $this->slug)->first()
			, $ref_number
		);
	}

	public function getNotifications($branch_name)
	{
		return $this->where( 'role_name', $branch_name )
			->orderBy( 'created_at', 'DESC' );
	}

	public function getUnreads($branch_id, $role, $pn, $user_id)
	{
		return $this->defineQuery( $pn, $user_id, $branch_id, $role );

	}

	public function getUnreadsMobile($branch_id, $role, $pn, $user_id, $limit, $count = null)
	{
		$query = $this->defineQuery( $pn, $user_id, $branch_id, $role );

		if ( !empty($count) ) {
			if( !empty($query) ) {
				\Log::info("===Masuk Query ada!===");
				\Log::info("===count notif: ".$query->whereNull('notifications.read_at')->count());
				return $query->where('notifications.is_read', false)->count();
			}
			\Log::info("===Masuk Query tidak ada!===");
			return 0;
		}
		\Log::info("===Masuk $count tidak ada===");
		return $query->paginate($limit);
	}

	public function defineQuery( $pn, $user_id, $branch_id, $role )
	{
		$role  = ($role == 'mp') ? 'pinca' : $role;
		$pn    = substr( $pn, -8 );

		$return = array(
			'pinca' => $branch_id
			, 'ao' => $pn
			, 'customer' => $user_id
			// , 'staff' => null
			, 'staff' => $pn
			, 'collateral-appraisal' => $branch_id
			, 'collateral' => $branch_id
			, 'developer' => $user_id
		);

		if ( isset( $return[ $role ] ) ) {
			$query = $this->orderBy('notifications.created_at', 'DESC');

			foreach ($return as $key => $value) {
				if ( $role == $key ) {
					$query->{ str_replace('-', '', $key) }( $value );
				}
			}

			if ( in_array( $role, ['pinca', 'ao'] ) ) {
				$query->select('notifications.id', 'notifications.type', 'notifications.notifiable_id', 'notifications.notifiable_type', 'notifications.data', 'notifications.read_at', 'notifications.created_at', 'notifications.updated_at', 'notifications.branch_id', 'notifications.role_name', 'notifications.slug','notifications.type_module','notifications.is_read', 'eforms.is_approved', 'eforms.ao_id', 'eforms.ref_number');
			}

			return $query;
		}

		return null;
	}

	public function defineUrl( $ref_number, $user_id )
	{
		if ( $user_id ) {
			return env('MAIN_APP_URL', 'http://mybri.bri.co.id/') . 'tracking/detail/' . $this->slug;
		}

		return env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/') . 'eform?ref_number=' . $ref_number . '&slug=' . $this->slug . '&type=' . $this->type_module;
	}

	public function defineData( $data )
	{
		return !empty( $data ) ? $data : null;
	}

	public function defineSubject( $url, $status_eform, $typeModuleCollateral, $approvalDataChange, $ref_number )
	{
		$pincaPosition = '';
		$pincaName = '';
		$aoName = '';
		$aoPosition = '';
		$baseWording = '';
		$coloring = '';
		$collateralManager = '';
		$collateralAppraisal = '';
		$debitur = '';
		$pengajuan = '';

		if ( isset( $this->data['eform_id'] ) ) {
			$eform = EForm::find($this->data['eform_id']);
			$pincaPosition = strtoupper( !empty($eform->pinca_position) ? $eform->pinca_position : '' );
			$pincaName = strtoupper( !empty($eform->pinca_name) ? $eform->pinca_name : ''  );
			$aoPosition = strtoupper( !empty($eform->ao_position) ? $eform->ao_position : '' );
			$aoName = strtoupper( !empty($eform->ao_name) ? $eform->ao_name : '' );
			$staffPosition = strtoupper( !empty($eform->staff_position) ? $eform->staff_position : '' );
			$staffName = strtoupper( !empty($eform->staff_name) ? $eform->staff_name : '' );

			if ( isset($eform->recontest) ) {
				$baseWording = 'Rekontes a.n ';

			} else {
				$baseWording = strtoupper( !empty($eform->product_type) ? $eform->product_type : '' ) . ' a.n ';

			}

			$baseWording .= $this->data['user_name'] . ' (' . $this->data['ref_number'] . ')';

			$coloring = !empty($eform->prescreening_status) ? $eform->prescreening_status : '' ;
			$getKPR = KPR::where( 'eform_id', $this->slug )->first();
			$plafondKredit = !empty($getKPR->request_amount) ? $getKPR->request_amount : 0;

			$pengajuan = 'Pengajuan ' . $baseWording . ' berhasil ditambahkan';
			if ( $staffName != "" ) {
				$pengajuan = 'Pengajuan baru ' . $baseWording . ' dari pengumpan ' . $staffName . ' mohon untuk dilakukan disposisi ke RM yang ditunjuk';

			} else if ( $aoName != '' ) {
				$pengajuan = 'Pengajuan ' . $baseWording . ' oleh RM ' . $aoName . ' berhasil ditambahkan. Saat ini dalam proses prakarsa oleh RM ' . $aoName;

			}
		}

		// $user = \RestwsHc::getUser();
		// \Log::info("=====LIST NOTIF WEB====");
		// \Log::info(isset($this->data['collateral_id']) ? $this->data['collateral_id'] : null);
		if ( isset( $this->data['collateral_id'] ) ) {
			
			// \Log::info('===MASUK KONDISI ISSET COLL_ID NOTIF===');
			$collateralData = Collateral::find($this->data['collateral_id']);
			// if(!$collateralData){
			// 	\Log::info('===MASUK KONDISI DLT LIST NOTIF WEB===');
			// 	$notifiable_id = $this->data['user_id'];
			// 	$collateral_id = $this->data['collateral_id'];
			// 	$type_module   = $this->data['type_module'];
			// 	\Log::info("==NOTIFIABLE_ID :".$notifiable_id);
			// 	\Log::info("==COLLATERAL_ID :".$collateral_id);
			// 	$deleteNotif = UserNotification::where('notifiable_id', $notifiable_id)
			// 					->where('type_module', $type_module)
			// 					// ->where('slug', $collateral_id)
			// 					->delete();

			// 	if($deleteNotif){

			// 		\Log::info($deleteNotif);
			// 		\Log::info("====SUCCESS===");

			// 	}else{

			// 		\Log::info($deleteNotif);
			// 		\Log::info("====FAILED===");

			// 	}

			// }else{
				
			// 	\Log::info('===MASUK KONDISI COLMAN ADA===');
			// 	$collateralManager = strtoupper( $collateralData->manager_name ? $collateralData->manager_name : $user['name'] );
			// }

			$collateralManager = strtoupper( $collateralData ? $collateralData->manager_name : "Collateral Manager" );
			
		}

		$approval_data_changes_id = $approvalDataChange ? $approvalDataChange->id : 0;
		$externalurl = env('MAIN_APP_URL', 'http://mybri.bri.co.id/');
		$internalurl = env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/');

		$managerKeys = array(
			'App\Notifications\CollateralManagerApprove'
			, 'App\Notifications\CollateralManagerRejected'
		);

		if ( in_array( $this->type, $managerKeys ) ) {
			$slug = $this->defineData( $this->data['prop_slug'] );

		}

		$managerAOKeys = array(
			'App\Notifications\CollateralManagerApproveAO'
			, 'App\Notifications\CollateralManagerRejectedAO'
		);

		if ( in_array( $this->type, $managerAOKeys ) ) {
			$slugAO = $this->defineData( $this->data['collateral_id'] );

		}

		$staffKeys = array(
			'App\Notifications\CollateralOTS'
			, 'App\Notifications\CollateralStafRejectOTS'
		);

		if ( in_array( $this->type, $staffKeys ) ) {
			$collateralAppraisal = $this->defineData( $this->data['staff_name'] );
			$debitur = $this->defineData( $this->data['user_name'] );

		}

		$subjectNotif = array(
			'message' => ''
			, 'message_external' => ''
			, 'url' => $url
			, 'url_mobile' => '#'
		);

		switch ( $this->type ) {
			case 'App\Notifications\PengajuanKprNotification':
				// Pengajuan baru
				// dari nasabah, ao, staff
				// ke pinca
				$append = array(
					'message' => $pengajuan
					, 'message_external' => 'Selamat, pengajuan kredit anda sukses dilakukan. petugas BRI akan segera menghubungi no HP yang telah anda daftarkan.'
				);
				break;

			case 'App\Notifications\EFormPenugasanDisposisi':
				// Disposisi
				// dari MP/Pinca
				// ke AO
				$append = array( 'message' => 'Disposisi Pengajuan ' . $baseWording . '. Segera tindak lanjut!!' );
				break;

			case 'App\Notifications\ApproveEFormCustomer':
				// Pengajuan di terima
				// dari MP/Pinca
				// ke nasabah
				$append = array(
					'message' => 'Customer Telah Menyetujui Form KPR'
					, 'message_external' => 'Customer Telah Menyetujui Form KPR'
				);

				if ( $status_eform == 'approved' ) {
					$append = array(
						'message' => 'Pengajuan ' . $baseWording . ' telah disetujui'
						, 'message_external' => 'Permohonan ' . $baseWording . ' dalam proses analisa oleh BRI'
					);

				}
				break;

			case 'App\Notifications\ApproveEFormInternal':
				// Pengajuan di terima oleh MP/Pinca di myBRI
				// dari MP/Pinca
				// ke ao
				$append = array( 'message' => 'Pengajuan ' . $baseWording . ' telah direkomendasi [' . $pincaPosition . '] untuk diproses lebih lanjut oleh CLS' );
				break;

			case 'App\Notifications\RejectEFormInternal':
				// Pengajuan di tolak oleh MP/Pinca
				// dari MP/Pinca
				// ke ao
				$append = array( 'message' => 'Pengajuan ' . $baseWording . ' tidak direkomendasi ' . $pincaPosition . ' untuk diproses lebih lanjut oleh CLS' );
				break;

			case 'App\Notifications\RejectEFormCustomer':
				// Pengajuan di tolak oleh MP/Pinca
				// dari MP/Pinca
				// ke nasabah
				$append = array(
					'message' => 'Pengajuan ' . $baseWording . ' tidak direkomendasi ' . $pincaPosition . ' untuk diproses lebih lanjut oleh CLS'
					, 'message_external' => 'Mohon maaf pengajuan ' . $baseWording . ' belum dapat kami setujui. Mohon hubungi tenaga pemasar kami untuk keterangan lebih lanjut.'
				);
				break;

			case 'App\Notifications\PencairanNasabah':
				// Proses pencairan dari CLAS
				// dari CLAS
				// ke nasabah
				$append = array(
					'message' => 'Selamat pencairan kredit ' . $baseWording . ' sebesar Rp. ' . number_format( $plafondKredit, 2 ) . ' telah berhasil'
					, 'message_external' => 'Selamat pencairan kredit ' . $baseWording . ' Anda sebesar Rp. ' . number_format( $plafondKredit, 2 ) . ' telah berhasil'
				);
				break;

			case 'App\Notifications\PencairanInternal':
				// Proses pencairan dari CLAS
				// dari CLAS
				// ke AO, MP/Pinca
				$append = array( 'message' => 'Selamat pencairan kredit ' . $baseWording . ' sebesar Rp. ' . number_format( $plafondKredit, 2 ) . ' telah berhasil' );
				break;

			case 'App\Notifications\ApproveEFormCLAS':
				// Pengajuan di terima di CLAS
				// dari CLAS
				// ke AO, MP/Pinca
				$append = array( 'message' => 'Pengajuan ' . $baseWording . ' telah disetujui ' . $pincaName . ' ' . $pincaPosition . ' dengan nonimal Rp. ' . number_format( $plafondKredit, 2 ) );
				break;

			case 'App\Notifications\ApproveEFormCLASCustomer':
				// Pengajuan di terima di CLAS
				// dari CLAS
				// ke Nasabah
				$append = array(
					'message' => 'Pengajuan ' . $baseWording . ' telah disetujui ' . $pincaName . ' ' . $pincaPosition . ' dengan nonimal Rp. ' . number_format( $plafondKredit, 2 )
					, 'message_external' => 'Selamat Permohonan ' . $baseWording . ' telah disetujui sebesar Rp. ' . number_format( $plafondKredit, 2 ) . ' Mohon siapkan dokumen yang diperlukan untuk penandatanganan akad kredit. Informasi lebih lanjut harap hubungi tenaga pemasar BRI'
				);
				break;

			case 'App\Notifications\RejectEFormCLAS':
				// Pengajuan di tolak di CLAS
				// dari CLAS
				// ke AO, MP/Pinca
				$append = array( 'message' => 'Mohon maaf pengajuan ' . $baseWording . ' belum dapat disetujui oleh CLS' );
				break;

			case 'App\Notifications\RejectEFormCLASCustomer':
				// Pengajuan di tolak di CLAS
				// dari CLAS
				// ke nasabah
				$append = array(
					'message' => 'Mohon maaf pengajuan ' . $baseWording . ' belum dapat disetujui oleh CLS'
					, 'message_external' => 'Mohon maaf pengajuan ' . $baseWording . ' belum dapat kami setujui. Mohon hubungi tenaga pemasar kami untuk keterangan lebih lanjut'
				);
				break;

			case 'App\Notifications\LKNEFormCustomer':
				// AO submit LKN
				// dari AO
				// ke MP/Pinca
				$append = array( 'message' => 'LKN RM ' . $aoName . ' atas pengajuan ' . $baseWording . ' telah dikirim dan menunggu persetujuan Anda.' );
				break;

			case 'App\Notifications\LKNEFormRecontest':
				// Klik recontest di CLAS
				// dari CLAS
				// ke MP/Pinca
				$append = array( 'message' => 'LKN Rekontes RM ' . $aoName . ' atas pengajuan ' . $baseWording . ' telah dikirim dan menunggu persetujuan Anda.' );
				break;

			case 'App\Notifications\ScorePefindoPreScreening':
				// Submit prescreening
				// dari petugas prescreening
				// ke AO
				$append = array( 'message' => 'Prescreening Calon Debitur a.n ' . $this->data['user_name'] . ' (' . $this->data['ref_number'] . ') telah selesai. Hasil Prescreening :  ' . $coloring );
				break;

			case 'App\Notifications\VerificationApproveFormNasabah':
				// Approve nasabah verifikasi
				// dari nasabah
				// ke AO
				$append = array( 'message' => 'Customer Telah Menyetujui Form KPR' );
				break;

			case 'App\Notifications\VerificationRejectFormNasabah':
				// Reject nasabah verifikasi
				// dari nasabah
				// ke AO
				$append = array( 'message' => 'Customer Telah Menolak Form KPR' );
				break;

			case 'App\Notifications\VerificationDataNasabah':
				// Submit verifikasi
				// dari AO
				// ke nasabah
				$append = array(
					'message' => 'Verifikasi Pengajuan KPR'
					, 'message_external' => 'Verifikasi Pengajuan KPR'
					, 'url' => $internalurl . 'verification?ref_number=' . $ref_number . '&slug=' . $this->slug
				);
				break;

			case 'App\Notifications\PropertyNotification':
				// get property
				// dari admin dev
				// ke col-man
				$append = array(
					'message' => 'Proyek Data Baru'
					, 'message_external' => 'Terdapat permohohonan penilaian agunan baru dari ' . $this->data['user_name'] . ' untuk Developer PKS BRI. Harap segera lakukan penilaian agunan sesuai ketentuan yang berlaku.'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\EditDeveloper':
				// perubahan data
				// dari
				// ke admin dev
				$append = array(
					'message' => 'Perbaharui Data Profile'
					, 'message_external' => 'Perbaharui Data Profile'
					, 'url' => $internalurl . '/approval-data/developer?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\ApproveDeveloperProfile':
				// approve perubahan data
				// dari
				// ke admin dev
				$append = array(
					'message' => 'Data Profile Telah Disetujui'
					, 'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
					, 'url' => $internalurl . '/dev/profile/personal?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\RejectDeveloperProfile':
				// reject perubahan data
				// dari
				// ke admin dev
				$append = array(
					'message' => 'Data Profile Telah Disetujui'
					, 'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
					, 'url' => $internalurl . '/dev/profile/personal?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\NewSchedulerCustomer':
				// tambah schedule
				// dari AO
				// ke nasabah
				$append = array(
					'message' => 'Schedule Data Baru'
					, 'message_external' => 'Schedule Data Baru'
					, 'url' => $externalurl . '/schedule?slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\UpdateSchedulerCustomer':
				// update schedule
				// dari AO
				// ke nasabah
				$append = array(
					'message' => 'Update Data Schedule'
					, 'message_external' => 'Mohon maaf terjadi perubahan jadwal pertemuan dengan tenaga pemasar kami. Detail informasi dapat dilihat di fitur penjadwalan dalam aplikasi MyBRI.'
					, 'url' => $externalurl . '/schedule?slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\RecontestEFormNotification':
				// Submit LKN recontest
				// dari CLAS
				// ke AO
				$append = array( 'message' => 'Mohon maaf pengajuan ' . $baseWording . ' belum dapat disetujui oleh CLS' );
				break;

			case 'App\Notifications\CollateralDisposition':
				// Disposisi collateral
				// dari col-man
				// ke staff-col
			\Log::info("==CLASS: App\Notifications\CollateralDisposition");
			\Log::info("==BASEWORDING: ".$baseWording);
				$append = array( 'message' => 'Disposisi Pengajuan ' . $baseWording . '. Segera tindak lanjut!!' );
				break;

			case 'App\Notifications\CollateraAODisposition':
				// Disposisi collateral
				// dari col-man
				// ke ao
			// \Log::info("==CLASS: App\Notifications\CollateraAODisposition");
			// \Log::info("==BASEWORDING: ".$baseWording."| Debitur: ".$this->data['user_name']);
			// \Log::info("==URL: ".$internalurl . 'staff-collateral?slug=' . $this->data['slug'] . '&type=' . $typeModuleCollateral);
				$append = array(
					// 'message' => 'Disposisi Pengajuan ' . $baseWording . '. Segera tindak lanjut!!'
					'message' => 'Penugasan Penilaian Agunan Debitur a.n ' . $this->data['user_name'] . '. Segera tindak lanjut!!'
					, 'url' => $internalurl . 'staff-collateral?slug=' . $this->data['slug'] . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralOTS':
				// Lakukan OTS
				// dari staff-col / AO
				// ke col-man
				$append = array(
					'message' => 'Penilaian agunan debitur a.n ' . $debitur . ' sedang dilakukan oleh ' . $collateralAppraisal
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafRejectOTS':
				// Reject OTS
				// dari staff-col / AO
				// ke col-man
				$append = array(
					'message' => 'Collateral appraisal a.n ' . $collateralAppraisal . ' menolak permintaan penilaian, harap lakukan penugasan ke staff collateral lainnya'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafPenilaianAnggunan':
				// Submit OTS
				// dari staff-col / AO
				// ke col-man
				$collateralStaf = $collateralAppraisal ? $collateralAppraisal : 'Staff Collateral' ;
				$append = array(
					'message' => 'Penilaian agunan debitur a.n ' . $debitur . ' telah dilakukan oleh ' . $collateralStaf . ', saat ini menunggu persetujauan Anda.'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafChecklist':
				// Upload dokument collateral
				// dari staff-col / AO
				// ke col-man
				$append = array(
					'message' => 'Collateral appraisal a.n ' . $collateralAppraisal . ' telah berhasil menambahkan dokumen Collateral Checklist.'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralManagerRejected':
				// reject collateral
				// dari col-man
				// ke admin dev
				$append = array(
				 	'message' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
				 	, 'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
					, 'url' => $externalurl . 'dev/proyek?slug=' . $slug . '&type=collateral_manager_approving'
				);
				break;

			case 'App\Notifications\CollateralManagerRejectedAO':
				// reject collateral
				// dari col-man
				// ke staff-col / AO
				$append = array(
				 	'message' => 'Penilaian agunan debitur a.n ' . $this->data['user_name'] . ' telah ditolak oleh collateral Manager' /*$collateralManager */
				 	, 'message_external' => 'Penilaian agunan debitur a.n ' . $debitur . ' telah ditolak oleh colllateral Manager '
				 	, 'url' => $internalurl . 'staff-collateral?slug=' . $slugAO . '&type=collateral_manager_rejecting'
				);
				break;

			case 'App\Notifications\CollateralManagerApprove':
				// approve collateral
				// dari col-man
				// ke admin dev
				$append = array(
				 	'message' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
				 	, 'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
					, 'url' => $externalurl . 'dev/proyek?slug=' . $slug . '&type=collateral_manager_approving'
				);
				break;

			case 'App\Notifications\CollateralManagerApproveAO':
				// approve collateral
				// dari col-man
				// ke staff-col / AO
				$append = array(
				 	'message' => 'Penilaian agunan debitur a.n ' . $this->data['user_name'] . ' telah disetujui oleh Collateral Manager ' /*. $collateralManager */
				 	, 'message_external' => 'Penilaian agunan debitur a.n ' . $debitur . ' telah disetujui oleh Collateral Manager ' /*. $collateralManager*/
					, 'url' => $internalurl . 'staff-collateral?slug=' . $slugAO . '&type=collateral_manager_approving'
				);
				break;

			default:
				$append = array(
					'message' => 'Type undefined'
					, 'url' => ''
				);
				break;
		}

		return array_merge($subjectNotif, $append);
	}
}