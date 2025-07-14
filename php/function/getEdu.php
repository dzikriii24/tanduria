<?php
$rss = simplexml_load_file("https://litbang.pertanian.go.id/rss.xml");

if ($rss && isset($rss->channel->item)) {
    $count = 0;
    foreach ($rss->channel->item as $item) {
        if ($count >= 5) break;
        echo '<div class="bg-white rounded-xl shadow p-4 mb-4">';
        echo '<h3 class="text-lg font-semibold text-[#1D6034]">' . htmlspecialchars($item->title) . '</h3>';
        echo '<p class="text-gray-700 text-sm mt-1 line-clamp-3">' . strip_tags($item->description) . '</p>';
        echo '<a href="' . htmlspecialchars($item->link) . '" target="_blank" class="text-blue-600 underline mt-2 inline-block">Baca selengkapnya</a>';
        echo '</div>';
        $count++;
    }
} else {
    echo "<p class='text-white'>Tidak ada berita tersedia saat ini.</p>";
}
?>
