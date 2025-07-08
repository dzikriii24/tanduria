<?php
function getSeasonName($month) {
    if (in_array($month, [10,11,12,1])) return "Musim Tanam 1";
    if (in_array($month, [2,3,4,5])) return "Musim Tanam 2";
    return "Musim Panen atau MT3";
}
?>