<div class="box weather-widget text-center" style="background: linear-gradient(135deg, #11998e, #38ef7d); color: white; position: relative; overflow: hidden;">
    <div class="animated-bg"></div>

    <div class="box-body" style="position: relative; z-index: 1;">
        <h3 class="box-title">🌦️ Weather Update</h3>

        <div style="font-size: 30px; margin-top: 10px;">
            {{ $weather['temperature'] ?? '--' }}°C
        </div>
        <div style="font-size: 16px; margin-top: 5px;">
            {{ $weather['condition'] ?? 'Loading weather...' }}
        </div>
        <div style="font-size: 14px; margin-top: 5px;">
            {{ $weather['location'] ?? 'Your Location' }}
        </div>

        {{-- Long Date and Live Clock --}}
        <div style="margin-top: 12px;">
            <div id="weather-date" style="font-size: 14px;"></div>
            <div id="weather-clock" style="font-size: 16px; font-weight: bold;"></div>
        </div>
    </div>
</div>
<script>
    function updateWeatherClock() {
        const now = new Date();

        // Format: Thursday, 2nd May 2025
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const longDate = now.toLocaleDateString(undefined, options);

        const time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

        document.getElementById('weather-date').textContent = longDate;
        document.getElementById('weather-clock').textContent = time;
    }

    setInterval(updateWeatherClock, 1000);
    updateWeatherClock(); // Initial call
</script>
