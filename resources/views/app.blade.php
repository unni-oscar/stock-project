<!DOCTYPE html>
<html>
<head>
    <title>Inertia SPA</title>
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <!-- <div id="app"></div> -->
    <div id="app" data-page="{{ json_encode($page) }}"></div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
