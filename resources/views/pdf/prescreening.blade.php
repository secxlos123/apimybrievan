<style type="text/css">
.card-box > img {
    height: 350px;
    width: 100%;
}
</style>
<div class="content-page">
    <div class="content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Form Prescreening</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Data Nasabah</h4>
                                </div>
                                <div class="col-md-12">
                                    <form class="form-horizontal" role="form">
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">NIK :</label>
                                            <div class="col-md-7">
                                                <p class="form-control-static">{{ $data['nik'] }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nama Calon Nasabah :</label>
                                            <div class="col-md-7">
                                                <p class="form-control-static">{{ $data['address'] }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Hasil Prescreening :</label>
                                            <div class="col-md-7">
                                                <p class="form-control-static">{{ $data['prescreening_status'] }}</p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Keterangan Terkait Risiko :</label>
                                            <div class="col-md-7">
                                                <p class="form-control-static">{{ $data['ket_risk'] }}</p>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>