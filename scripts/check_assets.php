<?php
$f = fopen('assets/videos/videowebsite.mp4', 'rb');
while (!feof($f)) {
    $buf = fread($f, 8);
    if (strlen($buf) < 8) break;
    $size = unpack('N', substr($buf, 0, 4))[1];
    $type = substr($buf, 4, 4);
    if ($type === 'moov') {
        echo "Found moov box at " . (ftell($f) - 8) . ", size $size\n";
        $moov = fread($f, $size - 8);
        $mvhdPos = strpos($moov, 'mvhd');
        if ($mvhdPos !== false) {
            $ver = ord($moov[$mvhdPos + 8]); // size(4) + type(4) + ver(1)
            if ($ver === 0) {
                // v0: ver(1) + flags(3) + creation(4) + mod(4) + timescale(4) + duration(4)
                $timescale = unpack('N', substr($moov, $mvhdPos + 8 + 12, 4))[1];
                $duration = unpack('N', substr($moov, $mvhdPos + 8 + 16, 4))[1];
            } else {
                $timescale = unpack('N', substr($moov, $mvhdPos + 8 + 20, 4))[1];
                $duration = unpack('J', substr($moov, $mvhdPos + 8 + 24, 8))[1];
            }
            echo "Timescale: $timescale, Duration: $duration (" . ($duration / $timescale) . " seconds)\n";
        }
        break;
    }
    if ($size > 1) {
        fseek($f, $size - 8, SEEK_CUR);
    } else {
        break;
    }
}
fclose($f);
