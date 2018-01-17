<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Lang;

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

	public function scopeUnreads($query) {
		return $query->whereNull('read_at');
	}

	public function notifiable() {
		return $this->morphTo();
	}

	/**
	 * Mark the notification as read.
	 *
	 * @return void
	 */
	public function markAsRead($is_read) {
		if (is_null($this->read_at)) {
			$this->forceFill(['read_at' => $this->freshTimestamp(),
								'is_read' => $is_read ])->save();
		}
	}

	public function getIsReadAttribute() {
		return (bool) $this->read_at;
	}

	public function getSubject($status_eform, $ref_number, $user_id) {

		$typeModuleEform = getTypeModule(EForm::class);
		$url = env('INTERNAL_APP_URL', 'http://internalmybri.bri.co.id/') . 'eform?ref_number=' . $ref_number . '&slug=' . $this->slug.'&type='.$typeModuleEform;
		if ($user_id) {
			$url = 'eform?slug=' . $this->slug.'&type='.$typeModuleEform;
		} else {
			$url = $url;
		}
		switch ($this->type) {
		/* eform  */
		case 'App\Notifications\PengajuanKprNotification':
			$subjectNotif = ['message' => 'Pengajuan KPR Baru',
				'url' => $url,
			];
			break;
		case 'App\Notifications\EFormPenugasanDisposisi':
			$subjectNotif = ['message' => 'Disposisi Pengajuan',
				'url' => $url,
			];
			break;
		case 'App\Notifications\ApproveEFormCustomer':
			if ($status_eform == 'approved') {
				$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Setujui',
					'url' => $url,
				];
			} else {
				$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
					'url' => $url,
				];
			}
			break;
		case 'App\Notifications\RejectEFormCustomer':
			$subjectNotif = ['message' => 'Pengajuan KPR Telah Di Tolak',
				'url' => $url,
			];
			break;
		case 'App\Notifications\LKNEFormCustomer':
			$subjectNotif = ['message' => 'Prakarsa LKN',
				'url' => $url,
			];
			break;
		case 'App\Notifications\VerificationApproveFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menyetujui Form KPR',
				'url' => $url,
			];
			break;
		case 'App\Notifications\VerificationRejectFormNasabah':
			$subjectNotif = ['message' => 'Customer Telah Menolak Form KPR',
				'url' => $url,
			];
			break;
		case 'App\Notifications\VerificationDataNasabah':
			$subjectNotif = ['message' => 'Verifikasi Pengajuan KPR',
				'url' => env('MAIN_APP_URL', 'http://mybri.bri.co.id/') . 'verification?ref_number=' . $ref_number . '&slug=' . $this->slug,
			];
			break;
		case 'App\Notifications\PropertyNotification':
			$subjectNotif = ['message' => 'Proyek Data Baru',
				'url' => '',
			];
			break;
		case 'App\Notifications\NewSchedulerCustomer':
			$subjectNotif = ['message' => 'Schedule Data Baru',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
			];
			break;
		case 'App\Notifications\UpdateSchedulerCustomer':
			$subjectNotif = ['message' => 'Update Data Schedule',
				'url' => '/schedule?slug=' . $this->slug.'&type='.$this->type_module,
			];
			break;
		default:
			$subjectNotif = ['message' => 'Type undefined',
				'url' => '',
			];
			break;
		}

		return $subjectNotif;
	}

	/**
	 * Get the notification all.
	 *
	 * @return void
	 */
	public function getNotifications($branch_name) {
		return $this->where('role_name', $branch_name)->orderBy('created_at', 'DESC');

	}

	public function getUnreads($branch_id, $role, $pn, $user_id) {
		$query = $this->leftJoin('eforms', 'notifications.slug', '=', 'eforms.id')
			->where('eforms.branch_id', @$branch_id)
			->Where('eforms.ao_id', @$pn)
			->orderBy('notifications.created_at', 'DESC');

		if (@$role == 'pinca') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\PengajuanKprNotification')) {
				$query->whereNull('eforms.ao_id')->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\LKNEFormCustomer')) {
				$query->leftJoin('visit_reports', 'eforms.id', '=', 'visit_reports.eform_id')
					->whereNotNull('visit_reports.created_at')
					->unreads();
			}

		}

		if (@$role == 'ao') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\EFormPenugasanDisposisi')) {
				$query->whereNotNull('eforms.ao_id')
					->whereNotNull('eforms.ao_name')
					->whereNotNull('eforms.ao_position')
					->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				/*is rejected*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationApproveFormNasabah')) {
				/*verifiy app*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationRejectFormNasabah')) {
				/*verifiy app*/
				$query->unreads();
			}
		}

		if (@$role == 'customer') {
			$query->where('notifications.notifiable_id', @$user_id);

			if ($query->Orwhere('notifications.type', 'App\Notifications\NewSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\UpdateSchedulerCustomer')) {
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\ApproveEFormCustomer')) {
				/*is is_approved*/
				$query->unreads();
			}

			if ($query->Orwhere('notifications.type', 'App\Notifications\RejectEFormCustomer')) {
				/*is rejected*/
				$query->unreads();
				if ($query->Orwhere('notifications.type', 'App\Notifications\VerificationDataNasabah')) {
					$query->unreads()->where('notifications.notifiable_id', @$user_id);
				}
			}
		}

		if (@$role == 'staff') {
			$query->whereNull('notifications.created_at');
		}

		if (@$role == 'collateral-appraisal') {
			$query->whereNull('notifications.created_at');
		}

		if (@$role == 'collateral') {
			if ($query->Orwhere('notifications.type', 'App\Notifications\PropertyNotification')) {
				$query->unreads();
			}
		}

		$query->select('notifications.id', 'notifications.type', 'notifications.notifiable_id', 'notifications.notifiable_type', 'notifications.data', 'notifications.read_at', 'notifications.created_at', 'notifications.updated_at', 'notifications.branch_id', 'notifications.role_name', 'notifications.slug','notifications.type_module','notifications.is_read', 'eforms.is_approved', 'eforms.ao_id', 'eforms.ref_number');

		return $query;
		
	}
}