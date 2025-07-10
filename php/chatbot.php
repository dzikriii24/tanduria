<?php 

$shortUrl = "https://maps.app.goo.gl/ARBAVcjn2TgpCN3s6";
$ch = curl_init($shortUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // penting: follow redirect
curl_exec($ch);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "Final URL: " . $finalUrl;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="fixed bottom-10 right-2 ">
        <script
            src='https://cdn.jotfor.ms/agent/embedjs/0197d993e310730e9d3145fd98455ecb6d21/embed.js?skipWelcome=1&maximizable=1'>
        </script>
        <script>
            window.addEventListener("DOMContentLoaded", function() {
                window.AgentInitializer.init({
                    rootId: "JotformAgent-0197d993e310730e9d3145fd98455ecb6d21",
                    formID: "0197d993e310730e9d3145fd98455ecb6d21",
                    queryParams: ["skipWelcome=1", "maximizable=1"],
                    domain: "https://www.jotform.com",
                    isInitialOpen: false,
                    isDraggable: false,
                    background: "linear-gradient(180deg, #6C73A8 0%, #6C73A8 100%)",
                    buttonBackgroundColor: "#0066C3",
                    buttonIconColor: "#FFFFFF",
                    variant: false,
                    customizations: {
                        greeting: "Yes",
                        greetingMessage: "Halo! Ada yang bisa saya bantu?",
                        pulse: "Yes",
                        position: "right"
                    }
                });
            });
        </script>

    </div>
</body>

</html>