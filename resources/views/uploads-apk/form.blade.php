<!DOCTYPE html>
<html>
<head>
	<title>upload your apk here</title>
	<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{!! asset('css/bootstrap.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/jquery.dataTables.min.css') !!}">
    <link rel="stylesheet" href="{!! asset('css/dataTables.bootstrap.min.css') !!}">
</head>
<body>
	@if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
        </div>
	@endif
	<form action="{{url('post-apk')}}" enctype="multipart/form-data" id="form">
	<div class="row">
		<div class="col-md-5 col-md-offset-1">
         	<div class="form-group">
	         	<label>APK Eksternal</label>
        		<input type="file" name="apkEks">
         	</div>
         	<div class="form-group">
	         	<label>APK Eksternal Version</label>
                <select name="versionInt">
                	<option value="00">Development</option>
                	<option value="01">Production</option>
                </select>
         	</div>
         	<div class="form-group">
	         	<label>APK Eksternal Version Number</label>
	         	<select name="versionNumEks">
	         		<?php for ($i=1; $i <= 1000; $i++) { ?>
	         			@if (strlen($i) == 1)
		         			<option value="000{{$i}}">{{$i}}</option>
		         		@elseif (strlen($i) == 2)
		         			<option value="00{{$i}}">{{$i}}</option>
		         		@elseif (strlen($i) == 3)
		         			<option value="0{{$i}}">{{$i}}</option>
		         		@else
		         			<option value="{{$i}}">{{$i}}</option>
		         		@endif
	         		<?php } ?>
	         	</select>
         	</div>
        </div>
        <div class="col-md-5">
        	<div class="form-group">
        		<label>APK Internal</label>
        		<input type="file" name="apkInt">
        	</div>
        	<div class="form-group">
	         	<label>APK Internal Version</label>
                <select name="versionInt">
                	<option value="00">Development</option>
                	<option value="01">Production</option>
                </select>
         	</div>
         	<div class="form-group">
	         	<label>APK Internal Version Number</label>
	         	<select name="versionNumInt">
	         		<?php for ($i=1; $i <= 1000; $i++) { ?>
		         		@if (strlen($i) == 1)
		         			<option value="000{{$i}}">{{$i}}</option>
		         		@elseif (strlen($i) == 2)
		         			<option value="00{{$i}}">{{$i}}</option>
		         		@elseif (strlen($i) == 3)
		         			<option value="0{{$i}}">{{$i}}</option>
		         		@else
		         			<option value="{{$i}}">{{$i}}</option>
		         		@endif
	         		<?php } ?>
	         	</select>
         	</div>
        </div>
	</div>
	<button type="submit">submit</button>

	</form>

	<script type="text/javascript" src="{!! asset('js/jquery.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/bootstrap.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/jquery.dataTables.min.js') !!}"></script>
    <script type="text/javascript" src="{!! asset('js/dataTables.bootstrap.js') !!}"></script>
</body>
</html>