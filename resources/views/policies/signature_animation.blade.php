<!-- Signing Animation Overlay -->
<div id="signingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(179,175,175,0.8); z-index: 1060; align-items: center; justify-content: center;">
    <div class="signing-container">
        <div class="signature-canvas">
            <img src="{{ asset('anim/pensignature.gif') }}" alt="Signature Animation" style="max-width: 100%; height: auto;">
        </div>
        <div class="signing-text">Signing...</div>
    </div>
</div>

<style>
.signing-container {
    text-align: center;
    color: white;
    max-width: 500px;
    margin: 0 auto;
}

.signature-canvas {
    margin: 20px 0;
    display: flex;
    justify-content: center;
}

.signing-text {
    font-size: 18px;
    font-weight: bold;
    margin-top: 5px;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.pen {
    transform-origin: center bottom;
}
</style>