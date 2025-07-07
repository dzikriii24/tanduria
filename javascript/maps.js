        function initMap() {
            const lokasi = {
                lat:  -6.175387128837032,
                lng: 106.82715121844436
            };

            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: lokasi,
            });

            const marker = new google.maps.Marker({
                position: lokasi,
                map: map,
                title: "Lokasi Lahan",
            });
        }