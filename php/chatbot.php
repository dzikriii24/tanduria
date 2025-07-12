<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="css/icon.css">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <title>Chatbot</title>
    <style type="text/tailwind">
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sora:wght@100..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/font.css">
    <link rel="stylesheet" href="css/hover.css">
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