<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Pompa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            text-align: center;
        }
        .dispenser-logo img {
            width: 100px;
            margin-bottom: 20px;
        }
        table {
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status-on {
            color: green;
            font-weight: bold;
        }
        .status-off {
            color: red;
            font-weight: bold;
        }
        .indicator {
            margin: 20px auto;
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .indicator div {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            line-height: 50px;
            color: white;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
        .hot {
            background-color: red;
        }
        .warm {
            background-color: orange;
        }
        .cold {
            background-color: blue;
        }
        .controls {
            margin: 20px auto;
            text-align: center;
        }
        .controls button {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
        }
        .slider-container {
            margin: 20px auto;
            text-align: center;
        }
        .slider-container input[type="range"] {
            width: 50%;
        }

        .glass {
            width: 150px;
            height: 300px;
            border: 5px solid #000;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
            margin: 20px auto;
            background-color: #e0e0e0;
        }

        .water {
            width: 100%;
            position: absolute;
            bottom: 0;
            background: linear-gradient(180deg, #00aaff, #0066cc);
            transition: height 1s ease-in-out; /* Tambahkan durasi transisi */
        }

    </style>
</head>
<body>
    <h1>Pantau Status Pompa</h1>
    <div class="container">
        <!-- Logo Dispenser -->
        <div class="dispenser-logo">
            <div class="glass">
                <div class="water" id="water"></div>
            </div>           
        </div>

        <!-- Tabel Status -->
        <table>
            <tr>
                <th>Status</th>
                <th>Jarak (cm)</th>
            </tr>
            <tr>
                {{-- Untuk mencegah Kerentanan XSS --}}
                <td class="{{ $status['pumpStatus'] === 'ON' ? 'status-on' : 'status-off' }}">
                    {{ htmlspecialchars($status['pumpStatus'], ENT_QUOTES, 'UTF-8') }}
                </td>
                <td>{{ htmlspecialchars($status['distance'], ENT_QUOTES, 'UTF-8') }} cm</td>                
            </tr>
        </table>

        <!-- Tombol Kontrol Manual -->
        <div class="indicator">
            <button class="hot">Panas</button>
            <button class="normal">Normal</button>
            <button class="cold">Dingin</button>
        </div>

        <!-- Slider untuk Mengatur Suhu -->
        <div class="slider-container">
            <label for="temperature">Atur Suhu:</label><br>
            <p id="temperature">Suhu: <span id="tempValue">50</span>°C</p>
        </div>
        
    </div>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // Update Value Slider
        const tempSlider = document.getElementById('temperature');
        const tempValue = document.getElementById('tempValue');

        // Menampilkan nilai suhu saat slider diubah
        tempSlider.addEventListener('input', () => {
            tempValue.textContent = tempSlider.value;
        });

        // Fungsi untuk mengubah suhu berdasarkan status tombol
        function changeTemperature(status) {
            console.log("Mengirim permintaan ke server untuk status:", status);
            
            fetch("{{ url('api/dispenser/change-temperature') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengubah suhu');
                }
                return response.json();
            })
            .then(data => {
                console.log("Respon server:", data);
                alert(data.response || 'Suhu berhasil diubah');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengubah suhu.');
            });

            let newTemperature;

            // Menentukan suhu berdasarkan tombol yang dipilih
            switch (status) {
                case 'Panas':
                    newTemperature = 80;
                    break;
                case 'Normal':
                    newTemperature = 50;
                    break;
                case 'Dingin':
                    newTemperature = 20;
                    break;
                default:
                    newTemperature = 50; // Default suhu Normal
            }

            // Set nilai slider dan update tampilan suhu
            tempSlider.value = newTemperature;
            tempValue.textContent = newTemperature;
        }

        // Menambahkan event listener ke tombol-tombol kontrol suhu
        document.querySelector('.hot').addEventListener('click', () => changeTemperature('Panas'));
        document.querySelector('.normal').addEventListener('click', () => changeTemperature('Normal'));
        document.querySelector('.cold').addEventListener('click', () => changeTemperature('Dingin'));
    });

        const fetchStatusAndUpdate = () => {
            fetch('http://172.20.10.2/dispenser/public/api/get-status')
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Data dari API:', data);
                    updateWaterLevel(data.pumpStatus, data.distance);
                    updateTable(data.pumpStatus, data.distance);
                })
                .catch(error => console.error('Error fetching pump status:', error));
        };

        // Panggil setiap 5 detik
        setInterval(fetchStatusAndUpdate, 5000);
    
        // Inisialisasi Pusher
        const pusher = new Pusher('bad981ecff11e5966b2b', {
            cluster: 'mt1',
            encrypted: true,
            forceTLS: true, // Pastikan TLS aktif
            disableStats: true, // Kurangi latensi dari pengiriman statistik
            enabledTransports: ['ws', 'wss'], // Hanya izinkan WebSocket
        });

        const channel = pusher.subscribe('pump-status');

        // Mendengarkan event 'PumpStatusUpdated' dan memperbarui tampilan
        channel.bind('PumpStatusUpdated', function(data) {
            console.log('Event diterima:', data);

            if (data && data.pumpStatus && data.distance !== undefined) {
                updateWaterLevel(data.pumpStatus, data.distance);
                updateTable(data.pumpStatus, data.distance);
            } else {
                console.error('Data tidak valid:', data);
            }
        });

        let updateTimeout;

        function debounceUpdate(callback, delay = 50) {
            clearTimeout(updateTimeout);
            updateTimeout = setTimeout(callback, delay);
        }

        function updateTable(pumpStatus, distance) {
            debounceUpdate(() => {
                const statusCell = document.querySelector('table tr:nth-child(2) td:nth-child(1)');
                const distanceCell = document.querySelector('table tr:nth-child(2) td:nth-child(2)');

                if (statusCell && distanceCell) {
                    statusCell.textContent = pumpStatus;
                    statusCell.className = pumpStatus === 'ON' ? 'status-on' : 'status-off';
                    distanceCell.textContent = `${distance} cm`;
                }
            });
        }


        let lastUpdate = 0;

        function updateWaterLevel(pumpStatus, distance) {
            const currentTime = new Date().getTime();
            if (currentTime - lastUpdate < 500) { // Membatasi pembaruan jika terlalu cepat
                return;
            }
            lastUpdate = currentTime;

            console.log(`Updating water level - Status: ${pumpStatus}, Distance: ${distance}`);
            const water = document.getElementById('water');
            const waterHeight = pumpStatus === 'ON' ? Math.min(100, 100 - distance) : 0;
            water.style.height = `${waterHeight}%`;
        }
            
        // Panggil fungsi secara berkala untuk sinkronisasi data
        setInterval(fetchStatusAndUpdate, 1000);

    </script>
</body>
</html>
