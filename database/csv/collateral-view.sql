CREATE VIEW collateral_view_table AS
SELECT
users.first_name,
users.last_name,
users.phone,
users.mobile_phone,
users.gender,
users.email,
customer_details.mother_name,
customer_details.address,
customer_details.citizenship_name,
customer_details.dependent_amount,
eforms.id AS eform_id,
eforms.ref_number,
eforms.ao_name,
eforms.branch,
eforms.appointment_date,
eforms.product_type,
eforms.is_approved,
eforms.nik,
kpr.status_property,
kpr.developer_id,
kpr.property_id,
kpr.price,
kpr.building_area,
kpr.home_location,
kpr.year,
kpr.active_kpr,
kpr.dp,
kpr.request_amount,
kpr.developer_name,
kpr.property_name,
kpr.kpr_type_property,
kpr.property_type,
kpr.property_type_name,
kpr.is_sent,
collaterals.id AS collaterals_id,
collaterals.staff_id,
collaterals.staff_name,
collaterals.status,
collaterals.is_staff,
collaterals.approved_by,
collaterals.created_at,
(select name from cities where cities.id = customer_details.birth_place_id) AS birthplace,
(select region_id from properties where properties.id = kpr.property_id ) AS region_id,
CASE WHEN kpr.active_kpr = 3 THEN '>2'
    WHEN kpr.active_kpr = 2 THEN '2'
    WHEN kpr.active_kpr = 1 THEN '1'
    ELSE 'Tidak Ada' END AS active_kpr_preview,
CASE WHEN (eforms.is_approved = false AND eforms.recommended = true) OR eforms.status_eform = 'Rejected' THEN 'Kredit Ditolak'
    WHEN eforms.status_eform = 'Approval1' THEN 'Kredit Disetujui'
    WHEN eforms.status_eform = 'Approval2' THEN 'Rekontes Kredit'
    WHEN eforms.is_approved = true THEN 'Proses CLS'
    WHEN visit_reports.id is not null THEN 'Prakarsa'
    WHEN eforms.ao_id is not null THEN 'Disposisi Pengajuan'
    ELSE 'Pengajuan Kredit' END AS status_tracking,
CASE WHEN customer_details.address_status::int = 0 THEN 'Milik Sendiri'
    WHEN customer_details.address_status::int = 1 THEN 'Milik Orang Tua/Mertua atau Rumah Dinas'
    WHEN customer_details.address_status::int = 3 THEN 'Tinggal di Rumah Kontrakan'
    ELSE 'Tidak Ada' END AS address_status,
CASE WHEN customer_details.status::int = 1 THEN 'Belum Menikah'
    WHEN customer_details.status::int = 2 THEN 'Menikah'
    WHEN customer_details.status::int = 3 THEN 'Janda/Duda'
    ELSE 'Tidak Ada' END AS status_user,
CASE WHEN kpr.status_property::int = 1 THEN 'Baru'
    WHEN kpr.status_property::int = 2 THEN 'Secondary'
    WHEN kpr.status_property::int = 3 THEN 'Refinancing'
    WHEN kpr.status_property::int = 4 THEN 'Renovasi'
    WHEN kpr.status_property::int = 5 THEN 'Top Up'
    WHEN kpr.status_property::int = 6 THEN 'Take Over'
    WHEN kpr.status_property::int = 7 THEN 'Take Over Top Up'
    ELSE 'Tidak Ada' END AS status_property_name,
CASE WHEN kpr.kpr_type_property::int = 1 THEN 'Rumah Tapak'
    WHEN kpr.kpr_type_property::int = 2 THEN 'Rumah Susun/Apartment'
    WHEN kpr.kpr_type_property::int = 3 THEN 'Rumah Toko'
    ELSE 'Tidak Ada' END AS kpr_type_property_name,
CASE WHEN eforms.send_clas_date is not null THEN age(eforms.send_clas_date,eforms.created_at::date)
    ELSE age(eforms.created_at::date) END AS aging

FROM users
LEFT JOIN customer_details ON customer_details.user_id = users.id
LEFT JOIN eforms ON eforms.user_id = users.id
LEFT JOIN kpr ON kpr.eform_id = eforms.id
LEFT JOIN visit_reports ON visit_reports.eform_id = eforms.id
LEFT JOIN collaterals ON collaterals.property_id = kpr.property_id
WHERE eforms.id is not null AND collaterals.id is not null AND visit_reports.id is not null
GROUP BY users.id , customer_details.id , eforms.id , kpr.id , visit_reports.id , collaterals.id
ORDER BY CASE WHEN eforms.status_eform in ('Rejected') THEN 5
        WHEN collaterals.status in ('ditolak') THEN 4
        WHEN eforms.status_eform in ('Approval2', 'Approval1', 'approved') THEN 3
        WHEN collaterals.staff_id is not null THEN 2
        ELSE 1 END,
        eforms.created_at ASC;