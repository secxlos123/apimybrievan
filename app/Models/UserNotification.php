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
		return $query->unreads()
			->leftJoin('visit_reports', 'eforms.id', '=', 'visit_reports.eform_id')
			->whereNotNull('visit_reports.created_at')
			->where( 'eforms.branch_id', $branch_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\PengajuanKprNotification'
					, 'App\Notifications\LKNEFormCustomer'
					, 'App\Notifications\LKNEFormRecontest'
					, 'App\Notifications\PencairanInternal'
					, 'App\Notifications\RecontestEFormNotification'
					, 'App\Notifications\ApproveEFormCLAS'
					, 'App\Notifications\RejectEFormCLAS'
				)
			);
	}

	public function scopeAo( $query, $pn )
	{
		return $query->unreads()
			->where( 'eforms.ao_id', $pn )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\EFormPenugasanDisposisi'
					, 'App\Notifications\ApproveEFormCustomer'
					, 'App\Notifications\RejectEFormCustomer'
					, 'App\Notifications\VerificationApproveFormNasabah'
					, 'App\Notifications\VerificationRejectFormNasabah'
					, 'App\Notifications\EditDeveloper'
					, 'App\Notifications\CollateraAODisposition'
					, 'App\Notifications\ScorePefindoPreScreening'
					, 'App\Notifications\ApproveEFormInternal'
					, 'App\Notifications\RejectEFormInternal'
					, 'App\Notifications\PencairanInternal'
					, 'App\Notifications\RecontestEFormNotification'
					, 'App\Notifications\RejectEFormCLAS'
				)
			);
	}

	public function scopeCustomer( $query, $user_id )
	{
		return $query->unreads()
			->where( 'notifications.notifiable_id', $user_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\RecontestEFormNotification'
					, 'App\Notifications\NewSchedulerCustomer'
					, 'App\Notifications\UpdateSchedulerCustomer'
					, 'App\Notifications\ApproveEFormCustomer'
					, 'App\Notifications\ApproveEFormCLAS'
					, 'App\Notifications\RejectEFormCustomer'
					, 'App\Notifications\RejectEFormCLAS'
					, 'App\Notifications\PencairanNasabah'
					, 'App\Notifications\EFormPenugasanDisposisi'
					, 'App\Notifications\ScorePefindoPreScreening'
				)
			)->orWhere( function( $q ) {
				$q->where('notifications.type', 'App\Notifications\PengajuanKprNotification')
					->whereNull('eforms.ao_id');
			});
	}

	public function scopeStaff( $query, $blank )
	{
		return $query->whereNull('notifications.created_at');

	}

	public function scopeCollateralAppraisal( $query, $branch_id )
	{
		return $query->unreads()
			->where( 'notifications.branch_id', $branch_id )
			->where( 'notifications.type', 'App\Notifications\CollateralDisposition' );
	}

	public function scopeCollateral( $query, $branch_id )
	{
    	return $query->unreads()
    		->where( 'notifications.branch_id', $branch_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\PropertyNotification'
					, 'App\Notifications\CollateralOTS'
					, 'App\Notifications\CollateralStafPenilaianAnggunan'
					, 'App\Notifications\CollateralStafRejectOTS'
					, 'App\Notifications\CollateralStafChecklist'
				)
			);
	}

	public function scopeDeveloper( $query, $user_id )
	{
		return $query->unreads()
			->where( 'notifications.notifiable_id', $user_id )
			->whereIn(
				'notifications.type'
				, array(
					'App\Notifications\CollateralManagerRejected'
					, 'App\Notifications\CollateralManagerApprove'
					, 'App\Notifications\ApproveDeveloperProfile'
					, 'App\Notifications\RejectDeveloperProfile'
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
		);
	}

	public function getNotifications($branch_name)
	{
		return $this->where( 'role_name', $branch_name )
			->orderBy( 'created_at', 'DESC' );
	}

	public function getUnreads($branch_id, $role, $pn, $user_id) {
		$pn = substr( $pn, -8 );
		$query = $this->leftJoin( 'eforms', 'notifications.slug', '=', 'eforms.id' )
			->orderBy('notifications.created_at', 'DESC');

		$return = array(
			'pinca' => $branch_id
			, 'ao' => $pn
			, 'customer' => $user_id
			, 'staff' => null
			, 'collateral-appraisal' => $branch_id
			, 'collateral' => $branch_id
			, 'developer' => $user_id
		);

		foreach ($return as $key => $value) {
			if ( $role == $key ) {
				$query->{ str_replace('-', '', $key) }( $value );
			}
		}

		$query->select('notifications.id', 'notifications.type', 'notifications.notifiable_id', 'notifications.notifiable_type', 'notifications.data', 'notifications.read_at', 'notifications.created_at', 'notifications.updated_at', 'notifications.branch_id', 'notifications.role_name', 'notifications.slug','notifications.type_module','notifications.is_read', 'eforms.is_approved', 'eforms.ao_id', 'eforms.ref_number');
		\Log::info($query->getBindings());
		\Log::info($query->toSql());
		return $query;
	}

	public function getUnreadsMobile($branch_id, $role, $pn, $user_id, $limit, $count = null) {
		$pn = substr( $pn, -8 );
		$query = new UserNotification;
		if (empty($pn)) {
			if ($role == 'customer') {
				$data = $query->where('notifiable_id', $user_id)
							  ->whereIn('type', [
								'App\Notifications\PengajuanKprNotification',
								'App\Notifications\RecontestEFormNotification',
								'App\Notifications\NewSchedulerCustomer',
								'App\Notifications\UpdateSchedulerCustomer',
								'App\Notifications\ApproveEFormCustomer',
								'App\Notifications\RejectEFormCustomer',
								'App\Notifications\PencairanNasabah',
   					   ]);
			}else if($role == 'developer') {
				$data = $query->where('notifiable_id', $user_id)
							  ->whereIn('type', [
								'App\Notifications\CollateralManagerRejected',
								'App\Notifications\CollateralManagerApprove',
								'App\Notifications\ApproveDeveloperProfile',
								'App\Notifications\RejectDeveloperProfile'
					   ]);
			}
		}else{
			if ($role == 'pinca') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
							'App\Notifications\ApproveEFormCLAS',
							'App\Notifications\RejectEFormCLAS',
							'App\Notifications\PencairanInternal',
							'App\Notifications\PengajuanKprNotification',
							'App\Notifications\LKNEFormCustomer',
							'App\Notifications\LKNEFormRecontest',
						]);
			}

			if ($role == 'ao') {
				$data = $query
				->select('notifications.id', 'notifications.type', 'notifications.notifiable_id',
									   'notifications.notifiable_type', 'notifications.data', 'notifications.read_at',
									   'notifications.created_at', 'notifications.updated_at',
									   'notifications.branch_id', 'notifications.role_name', 'notifications.slug',
									   'notifications.type_module','notifications.is_read', 'eforms.is_approved',
									   'eforms.ao_id', 'eforms.ref_number')
							  ->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
				  	          ->where('eforms.branch_id', $branch_id)
							  ->where('eforms.ao_id', $pn)
							  ->whereIn('notifications.type', [
							  		'App\Notifications\EFormPenugasanDisposisi',
									'App\Notifications\ApproveEFormInternal',
									'App\Notifications\ApproveEFormCLAS',
									'App\Notifications\RejectEFormCLAS',
									'App\Notifications\RejectEFormInternal',
									'App\Notifications\VerificationApproveFormNasabah',
									'App\Notifications\VerificationRejectFormNasabah',
									'App\Notifications\EditDeveloper',
									'App\Notifications\CollateraAODisposition',
									'App\Notifications\ScorePefindoPreScreening',
									'App\Notifications\PencairanInternal',
									'App\Notifications\LKNEFormRecontest',
					  	]);
			}

			if ($role == 'staff') {
				$data = $query->where('branch_id', $branch_id);
			}

			if ($role == 'collateral-appraisal') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
							  		'App\Notifications\CollateralDisposition',
					    ]);
            }

			if ($role == 'collateral') {
				$data = $query->where('branch_id', $branch_id)
							  ->whereIn('type', [
									'App\Notifications\PropertyNotification',
									'App\Notifications\CollateralOTS',
									'App\Notifications\CollateralStafPenilaianAnggunan',
									'App\Notifications\CollateralStafRejectOTS',
									'App\Notifications\CollateralStafChecklist',
				]);
			}
		}

		if (empty($count)) {
			return $data->orderBy('created_at', 'desc')->paginate($limit);
		}else {
			if(empty($data)){
				$counter = count($data);
			}else {
				$counter = $data->get()->count();
			}

			return $counter;
		}
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

	public function defineSubject( $url, $status_eform, $typeModuleCollateral, $approvalDataChange )
	{
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
				$append = array(
					'message' => 'Pengajuan Aplikasi KPR Baru'
					, 'message_external' => 'Selamat, pengajuan kredit anda sukses dilakukan. petugas BRI akan segera menghubungi no HP yang telah anda daftarkan.'
				);
				break;

			case 'App\Notifications\EFormPenugasanDisposisi':
				// Disposisi dari MP/Pinca ke AO
				$append = array( 'message' => 'Disposisi Pengajuan' );
				break;

			case 'App\Notifications\ApproveEFormCustomer':
				// Pengajuan di terima untuk costumer
				$append = array( 'message' => 'Customer Telah Menyetujui Form KPR' );

				if ( $status_eform == 'approved' ) {
					$append = array(
						'message' => 'Pengajuan KPR Telah Di Setujui'
						, 'message_external' => 'Permohonan KPR an. ' . $this->data['user_name'] . ' no : ' . $this->data['ref_number'] . ' dalam proses analisa oleh BRI.'
					);

				}
				break;

			case 'App\Notifications\ApproveEFormInternal':
				// Pengajuan di terima oleh MP/Pinca di myBRI
				$append = array( 'message' => 'Pengajuan KPR Telah Di Setujui' );
				break;

			case 'App\Notifications\RejectEFormInternal':
				// Pengajuan di tolak oleh MP/Pinca
				$append = array( 'message' => 'Pengajuan KPR Telah Di Tolak' );
				break;

			case 'App\Notifications\PencairanNasabah':
				// Proses pencairan dari CLAS dan di kirim ke customer
				$append = array( 'message' => 'Pengajuan KPR Telah Dicairkan' );
				break;

			case 'App\Notifications\PencairanInternal':
				// Proses pencairan dari CLAS dan di kirim ke Internal
				$append = array( 'message' => 'Pengajuan KPR Telah Dicairkan' );
				break;

			case 'App\Notifications\ApproveEFormCLAS':
				// Pengajuan di terima oleh MP/Pinca di CLAS
				$getKPR = KPR::where( 'eform_id', $this->slug )->first();
				$plafondKredit = $getKPR->request_amount ? $getKPR->request_amount : 0;

				$append = array(
					'message' => 'Selamat Permohonan KPR an. ' . $this->data['user_name'] . ' no : ' . $this->data['ref_number'] . ' telah disetujui sebesar RP. ' . number_format( $plafondKredit, 2 ) . ' Mohon siapkan dokumen yang diperlukan untuk penandatanganan akad kredit. Informasi lebih lanjut harap hubungi tenaga pemasar BRI'
				);
				break;

			case 'App\Notifications\RejectEFormCustomer':
				$append = array( 'message' => 'Pengajuan KPR Telah Ditolak' );
				break;

			case 'App\Notifications\RejectEFormCLAS':
				$append = array( 'message' => 'Mohon maaf pengajuan KPR an. ' . $this->data['user_name'] . ' no : ' . $this->data['ref_number'] . ' belum dapat kami setujui. Mohon hubungi tenaga pemasar kami untuk keterangan lebih lanjut.' );
				break;

			case 'App\Notifications\LKNEFormCustomer':
				$append = array( 'message' => 'Prakarsa LKN' );
				break;

			case 'App\Notifications\LKNEFormRecontest':
				$append = array( 'message' => 'LKN Recontest' );
				break;

			case 'App\Notifications\LKNEFormCLAS':
				$append = array( 'message' => 'Prakarsa LKN' );
				break;

			case 'App\Notifications\ScorePefindoPreScreening':
				$append = array( 'message' => 'Eform Pengajuan Telah Diisi Score Pefindo' );
				break;

			case 'App\Notifications\VerificationApproveFormNasabah':
				$append = array( 'message' => 'Customer Telah Menyetujui Form KPR' );
				break;

			case 'App\Notifications\VerificationRejectFormNasabah':
				$append = array( 'message' => 'Customer Telah Menolak Form KPR' );
				break;

			case 'App\Notifications\VerificationDataNasabah':
				$append = array(
					'message' => 'Verifikasi Pengajuan KPR'
					, 'url' => $internalurl . 'verification?ref_number=' . $ref_number . '&slug=' . $this->slug
				);
				break;

			case 'App\Notifications\PropertyNotification':
				$append = array(
					'message' => 'Proyek Data Baru'
					, 'message_external' => 'Terdapat permohohonan penilaian agunan baru dari ' . $this->data['user_name'] . ' untuk Developer PKS BRI. Harap segera lakukan penilaian agunan sesuai ketentuan yang berlaku.'
				);
				break;

			case 'App\Notifications\NewSchedulerCustomer':
				$append = array(
					'message' => 'Schedule Data Baru'
					, 'message_external' => 'Schedule Data Baru'
					, 'url' => $externalurl . '/schedule?slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\UpdateSchedulerCustomer':
				$append = array(
					'message' => 'Update Data Schedule'
					, 'message_external' => 'Mohon maaf terjadi perubahan jadwal pertemuan dengan tenaga pemasar kami. Detail informasi dapat dilihat di fitur penjadwalan dalam aplikasi MyBRI.'
					, 'url' => $externalurl . '/schedule?slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\EditDeveloper':
				$append = array(
					'message' => 'Perbaharui Data Profile'
					, 'url' => $internalurl . '/approval-data/developer?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\ApproveDeveloperProfile':
				$append = array(
					'message' => 'Data Profile Telah Disetujui'
					, 'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
					, 'url' => $internalurl . '/dev/profile/personal?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\RejectDeveloperProfile':
				$append = array(
					'message' => 'Data Profile Telah Disetujui'
					, 'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
					, 'url' => $internalurl . '/dev/profile/personal?related_id=' . $approval_data_changes_id . '&slug=' . $this->slug . '&type=' . $this->type_module
				);
				break;

			case 'App\Notifications\RecontestEFormNotification':
				$append = array( 'message' => 'Pengajuan Anda Telah di Rekontest' );
				break;

			case 'App\Notifications\CollateralDisposition':
				$append = array( 'message' => 'Penugasan Staff Collateral' );
				break;

			case 'App\Notifications\CollateraAODisposition':
				$append = array(
					'message' => 'Penugasan AO Collateral'
					, 'url' => $internalurl . 'staff-collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralOTS':
				$append = array(
					'message' => 'Penilaian agunan debitur an. [' . $debitur . '] sedang dilakukan oleh [' . $collateralAppraisal . ']'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafRejectOTS':
				$append = array(
					'message' => 'Collateral appraisal an [' . $collateralAppraisal . '] menolak permintaan penilaian, harap lakukan penugasan ke staff collateral lainnya'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafPenilaianAnggunan':
				$append = array(
					'message' => 'Form Penilaian Agunan'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralStafChecklist':
				$append = array(
					'message' => 'Collateral Checklist'
					, 'url' => $internalurl . 'collateral?slug=' . $this->slug . '&type=' . $typeModuleCollateral
				);
				break;

			case 'App\Notifications\CollateralManagerRejected':
				$append = array(
				 	'message' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
				 	, 'message_external' => 'Mohon maaf daftar property anda belum dapat kami tayangkan. Pastikan data property anda dan isi PKS dengan BRI telah sesuai. Info lebih lanjut hubungi Staff Business Relations BRI'
					, 'url' => $externalurl . 'dev/proyek?slug=' . $slug . '&type=collateral_manager_approving'
				);
				break;

			case 'App\Notifications\CollateralManagerApprove':
				$append = array(
				 	'message' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
				 	, 'message_external' => 'Selamat, daftar property anda telah tayang di aplikasi MyBRI, kini properti anda dapat dilihat oleh member dan visitor MyBRI. Apabila ada perubahan harga dan detail data properti harap segera lakukan perubahan'
					, 'url' => $externalurl . 'dev/proyek?slug=' . $slug . '&type=collateral_manager_approving'
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