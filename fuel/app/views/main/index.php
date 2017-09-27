<html>
<head>
    <title>Angular 2 QuickStart</title>
    <base href="<?php echo Uri::create('/') ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- 1. Load libraries -->
    <!-- Polyfill(s) for older browsers -->
    <script src="https://npmcdn.com/core-js@^2.4.1/client/shim.min.js"></script>
    <script src="https://npmcdn.com/zone.js@0.6.12/dist/zone.js"></script>
    <script src="https://npmcdn.com/reflect-metadata@0.1.2/Reflect.js"></script>
    <script src="https://npmcdn.com/systemjs@0.19.35/dist/system.src.js"></script>
    <!-- 2. Configure SystemJS -->
    <script src="client/src/systemjs.config.js"></script>
    <script>
        System.import('client/src/dist/bundle.js').then(function() {
            System.import('main').catch(function(err){ console.error(err); });
        });
    </script>
</head>
<!-- 3. Display the application -->
<body>
<my-app>Loading...</my-app>
</body>
</html>


