Email : {!! $mail['email'] !!} <br />

@if (isset($mail[ 'url' ]))
    Aktivasi Akun : {!! $mail[ 'url' ] !!}
@endif

@if (isset($mail['password']))
    Password : {!! $mail['password'] !!}
@endif
