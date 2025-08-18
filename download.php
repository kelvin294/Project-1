<?php

session_start();

$dataFile = __DIR__ . '/data.json';

if (!file_exists($dataFile)) {

die("Data file not found");

}

$data = json_decode(file_get_contents($dataFile), true);

$movieName = isset($_GET['movie']) ? urldecode($_GET['movie']) : '';

$season = isset($_GET['season']) ? $_GET['season'] : null;

$episode = isset($_GET['ep']) ? $_GET['ep'] : null;

if (!$movieName) {

die("Invalid request");

}

$isSeries = isset($data['series'][$movieName]);

$isMovie = isset($data['movies'][$movieName]);

// Provider videos

$providerVideos = [

"HubCloud" => "Guild Video.mp4",

"FilePress " => "videos/filepress.mp4",

"GDflix" => "videos/gdflix.mp4",

"Google Drive" => "videos/googledrive.mp4",

"Gofile" => "videos/gofile.mp4"

];

// === Movie ===

if ($isMovie && !$isSeries) {

$url = $data['movies'][$movieName]['url'] ?? null;

if (!$url) die("File not found for $movieName");

$downloadUrl = $url;

$defaultVideo = reset($providerVideos);

goto OASIS_UI;

}

// === Series ===

if ($isSeries) {

$seasons = [];

foreach ($data['series'][$movieName] as $key => $epData) {

if (preg_match('/S(\d+)E(\d+)/', $key, $matches)) {

$seasonNum = 'Season ' . $matches[1];

$episodeNum = 'Episode ' . $matches[2];

$seasons[$seasonNum][$episodeNum] = $epData['url'];

}

}

// If season & episode chosen → show player

if ($season && $episode) {

if (isset($seasons[$season][$episode])) {

$downloadUrl = $seasons[$season][$episode];

$defaultVideo = reset($providerVideos);

goto OASIS_UI;

} else {

die("Episode not found");

}

}

// Show selection UI

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title><?= htmlspecialchars($movieName) ?> - Select Episode</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body {

background: #000;

color: #fff;

font-family: Arial, sans-serif;

text-align: center;

margin: 0;

padding: 0 8px;

}

h1, h2 { margin-top: 15px; }

.btn {

padding: 12px 20px;

margin: 6px;

font-size: 16px;

background: #444;

color: #fff;

border-radius: 5px;

display: inline-block;

text-align: center;

min-width: 100px;

box-sizing: border-box;

text-decoration: none;

}

.btn:hover {

background: orange;

}

@media (max-width: 600px) {

.btn {

font-size: 14px;

padding: 10px;

min-width: 80px;

}

}

</style>

</head>

<body>

<h1><?= htmlspecialchars($movieName) ?></h1>

<h2>Select a Season:</h2>

<?php foreach (array_keys($seasons) as $seasonName): ?>

<a class="btn" href="?movie=<?= urlencode($movieName) ?>&season=<?= urlencode($seasonName) ?>"><?= htmlspecialchars($seasonName) ?></a>

<?php endforeach; ?>

<?php if ($season && isset($seasons[$season])): ?>

<h2>Select an Episode:</h2>

<?php foreach ($seasons[$season] as $epName => $epUrl): ?>

<a class="btn" href="?movie=<?= urlencode($movieName) ?>&season=<?= urlencode($season) ?>&ep=<?= urlencode($epName) ?>"><?= htmlspecialchars($epName) ?></a>

<?php endforeach; ?>

<?php endif; ?>

</body>

</html>

<?php

exit;

}

die("Movie or series not found.");

// === OASiS Player UI ===

OASIS_UI:

$newVideo = "Guild Video.mp4"; // Hardcoded right-side video

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Download <?= htmlspecialchars($movieName) ?></title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body {

background: #000;

color: #fff;

font-family: Arial, sans-serif;

margin: 0;

padding: 0;

}

.oasis-title {

font-size: 28px;

font-weight: bold;

color: cyan;

animation: glow 1.5s infinite alternate;

margin-top: 15px;

}

@keyframes glow {

from { text-shadow: 0 0 10px cyan; }

to { text-shadow: 0 0 25px lime; }

}

.movie-name {

font-size: 16px;

margin-top: 5px;

color: #fff;

word-break: break-word;

padding: 0 10px;

}

.download-btn {

display: inline-block;

padding: 10px 20px;

font-size: 18px;

background: #28a745;

color: #fff;

text-decoration: none;

border-radius: 8px;

margin-top: 15px;

}

.download-btn:hover {

background: #ff6600;

}

.providers {

margin-top: 10px;

}

.provider {

display: inline-block;

margin: 3px;

padding: 5px 10px;

background: #222;

border-radius: 5px;

cursor: pointer;

font-size: 14px;

}

.provider:hover {

background: #0ff;

color: #000;

}

.video-container {

display: flex;

flex-wrap: wrap;

justify-content: center;

margin-top: 15px;

gap: 10px;

}

.video-half {

flex: 1 1 300px;

max-width: 100%;

aspect-ratio: 16/9;

}

video {

width: 100%;

height: 100%;

border: 2px solid #fff;

border-radius: 10px;

object-fit: cover;

}

.vlc-note {

font-size: 14px;

color: #ffcc00;

margin-top: 5px;

}

/* Mobile adjustments */

@media (max-width: 600px) {

.oasis-title { font-size: 22px; }

.movie-name { font-size: 14px; }

.download-btn { font-size: 16px; padding: 8px 16px; }

.provider { font-size: 12px; padding: 4px 8px; }

.video-container { flex-direction: column; gap: 8px; }

.video-half { flex: none; width: 100%; aspect-ratio: 16/9; max-height: 200px; }

}

</style>

<script>

let providerList = <?= json_encode(array_values($providerVideos)) ?>;

let currentIndex = 0;

function changeVideo(src, index = null) {

if (index !== null) currentIndex = index;

document.getElementById('provider-video').src = src;

document.getElementById('video-player').load();

document.getElementById('video-player').play();

}

function playNext() {

currentIndex++;

if (currentIndex >= providerList.length) currentIndex = 0;

changeVideo(providerList[currentIndex], currentIndex);

}

window.onload = function() {

let leftPlayer = document.getElementById('video-player');

leftPlayer.play().catch(() => {

leftPlayer.muted = true;

leftPlayer.play();

});

leftPlayer.addEventListener('ended', playNext);

}

</script>

</head>

<body>

<div class="oasis-title">OASiS</div>

<div class="movie-name"><?= htmlspecialchars($movieName) ?></div>

<a class="download-btn" href="<?= htmlspecialchars($downloadUrl) ?>">⬇ Download</a>

<div class="providers">

<?php $i=0; foreach ($providerVideos as $name => $video): ?>

<div class="provider" onclick="changeVideo('<?= htmlspecialchars($video) ?>', <?= $i ?>)"><?= htmlspecialchars($name) ?></div>

<?php $i++; endforeach; ?>

</div>

<div class="video-container">

<div class="video-half">

<video id="video-player" autoplay muted controls>

<source id="provider-video" src="<?= htmlspecialchars($defaultVideo) ?>" type="video/mp4">

Your browser does not support the video tag.

</video>

</div>

<div class="video-half">

<video autoplay controls>

<source src="<?= htmlspecialchars($newVideo) ?>" type="video/mp4">

Your browser does not support the video tag.

</video>

<div class="vlc-note">Use VLC app for better experience</div>

</div>

</div>

</body>

</html>