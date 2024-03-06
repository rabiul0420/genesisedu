<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

</head>
<body>

<p>Dear {{ $firstName }},</p>

<p>Your Driving License ({{ $licenseID }}) is expiring soon.  Please <a href="{{ url('identityverification') }}">click here</a> to  update your driving license. </p>
<br>
<p></p>

<p>Thanks,</p>

<p>GENESIS Support.</p>

</body>
</html>




