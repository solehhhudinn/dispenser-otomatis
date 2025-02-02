<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Status Pompa</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
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
            width: 200px;
            height: 350px;
            border-left: 4px solid rgb(54, 54, 54);
            border-right: 4px solid rgb(54, 54, 54);
            border-bottom: 4px solid rgb(54, 54, 54);
            border-bottom-left-radius: 50px;
            border-bottom-right-radius: 50px;
            position: relative;
            overflow: hidden;
            margin: 20px auto;
            background-color: whitesmoke;
        }

        .water {
            width: 100%;
            position: absolute;
            bottom: 0;
            background: linear-gradient(180deg, #00aaff, #0152a4);
            transition: height 1s ease-in-out;
        }

    </style>
</head>
<body>
    <nav class="bg-white fixed w-full z-20 top-0 start-0 border-b border-gray-200">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="/pump-status" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{asset('logo.jpg')}}" class="w-[35px]"  alt="Flowbite Logo">
            <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Smart Dispenser</span>
        </a>
        <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <div class="text-gray-400">Kelompok 3 IOT</div>
        </div>
        </div>
      </nav>
    <h1>Pantau Status Pompa</h1>
    <div class="max-w-screen-xl mx-auto p-4">
        <div class="bg-white rounded-xl p-10 mt-20" style="border: 1px solid rgb(232, 232, 232)">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-5 p-4 text-white">
                    <div class="dispenser-logo">
                        <div class="glass">
                            <div class="water" id="water"></div>
                        </div>           
                    </div>
                </div>
                <div class="col-span-7 p-4">
                    <h5 class="mb-3 mt-5">Status</h5>
                    <span class="{{ $status['pumpStatus'] === 'ON' ? 'text-green-600 bg-green-100' : 'text-red-600 bg-red-100' }} px-5 py-2 text-4xl font-semibold rounded-xl">                        
                        {{ htmlspecialchars($status['pumpStatus'], ENT_QUOTES, 'UTF-8') }}
                    </span>
                    <div class="text-lg mt-10 mb-2">
                        Jarak (cm) : <b>{{ htmlspecialchars($status['distance'], ENT_QUOTES, 'UTF-8') }} cm</b>
                    </div>              
                    <div class="bg-white p-4 text-center rounded-xl w-[300px] mb-4" style="border: 1px solid rgb(232, 232, 232)">
                        <label for="temperature" class="text-gray-400 text-sm">Atur Suhu:</label><br>
                        <p id="temperature" class="font-semibold text-3xl"><span id="tempValue">50</span>°C</p>
                    </div>
                    <button type="button" class="hot text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 shadow-lg shadow-red-500/50 dark:shadow-lg dark:shadow-red-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Panas</button>
                    <button type="button" class="normal text-white bg-gradient-to-r from-gray-400 via-gray-500 to-gray-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-gray-300 dark:focus:ring-gray-800 shadow-lg shadow-gray-500/50 dark:shadow-lg dark:shadow-gray-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">Normal</button>
                    <button type="button" class="cold text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 shadow-lg shadow-blue-500/50 dark:shadow-lg dark:shadow-blue-800/80 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 ">Dingin</button>                           
                </div>
            </div>

            {{-- <table>
                <tr>
                    <th>Status</th>
                    <th>Jarak (cm)</th>
                </tr>
                <tr>
                    <td class="{{ $status['pumpStatus'] === 'ON' ? 'status-on' : 'status-off' }}">
                        {{ htmlspecialchars($status['pumpStatus'], ENT_QUOTES, 'UTF-8') }}
                    </td>
                    <td>{{ htmlspecialchars($status['distance'], ENT_QUOTES, 'UTF-8') }} cm</td>                
                </tr>
            </table>

            <div class="indicator">
                <button class="hot">Panas</button>
                <button class="normal">Normal</button>
                <button class="cold">Dingin</button>
            </div>

            <div class="slider-container">
                <label for="temperature">Atur Suhu:</label><br>
                <p id="temperature">Suhu: <span id="tempValue">50</span>°C</p>
            </div> --}}
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
