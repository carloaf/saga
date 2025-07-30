<!DOCTYPE html>
<html>
<head>
    <title>Teste Timezone</title>
</head>
<body>
    <h1>Teste de Timezone</h1>
    <p>PHP Timezone: {{ date_default_timezone_get() }}</p>
    <p>PHP Date: {{ date('Y-m-d H:i:s T') }}</p>
    <p>Carbon Now: {{ \Carbon\Carbon::now()->format('Y-m-d H:i:s T') }}</p>
    <p>Config Timezone: {{ config('app.timezone') }}</p>
    <p>Laravel Timezone: {{ \Carbon\Carbon::now()->timezone }}</p>
</body>
</html>
