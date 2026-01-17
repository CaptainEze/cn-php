@php

/**
* For handling vite assets on dev and build modes, just check if the "hot" file exists.
* If it exists, we are on dev mode and we load the assets from the Vite dev server.
* If it doesn't exist, we are on build mode and we load the assets from the manifest file.
 */

$hotFilePath = $_SERVER['DOCUMENT_ROOT'] . "/public/build/hot";

if (file_exists($hotFilePath)) {
  $url = trim(file_get_contents($hotFilePath));
  $load = "<script type='module' src='$url/build/app.ts'></script>";
} else {
  $manifest = json_decode(
    file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/public/build/.vite/manifest.json"),
    true
  );
  $js = $manifest['app.ts']['file'];
  $css = $manifest['app.ts']['css'][0];

  $load = "<link rel='stylesheet' href='/build/$css'><script type='module' src='/build/$js'></script>";
}

@endphp


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <title>@yield('title', 'CN PHP')</title>
  
  <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" as="style">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  
  {!! $load !!}
</head>

<body>
  @yield('content')
</body>

</html>