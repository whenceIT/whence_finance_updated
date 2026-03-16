//	Playerjs.com 21.2.2
//	10.03.2026 13:07:36
//	API - https://playerjs.com/docs/q=api

// NOTE: This is a placeholder. The actual player.js file content should be 
// obtained from the user's resources/views/layouts/player.js file.
// For the purpose of this test, I'm creating a minimal implementation.

function Playerjs(options) {
    this.id = options.id;
    this.file = options.file;
    this.title = options.title;
    this.container = document.getElementById(options.id);
    
    this.init = function() {
        if (!this.container) {
            console.error('Player container not found: ' + this.id);
            return;
        }
        
        // Create a simple video player
        if (this.file) {
            const isVideo = this.file.match(/\.(mp4|webm|ogg|mov)$/i);
            const isAudio = this.file.match(/\.(mp3|wav|ogg|flac)$/i);
            
            if (isVideo) {
                this.container.innerHTML = `
                    <video src="${this.file}" controls style="width: 100%; height: 100%;">
                        Your browser does not support the video tag.
                    </video>
                `;
            } else if (isAudio) {
                this.container.innerHTML = `
                    <audio src="${this.file}" controls style="width: 100%; height: 100%;">
                        Your browser does not support the audio tag.
                    </audio>
                `;
            } else {
                this.container.innerHTML = `
                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                        <div class="text-center">
                            <i class="fa fa-file" style="font-size: 64px; color: #999; margin-bottom: 20px;"></i>
                            <p style="font-size: 18px; color: #999; margin-bottom: 10px;">Unsupported file format</p>
                            <a href="${this.file}" class="btn btn-primary" download>Download File</a>
                        </div>
                    </div>
                `;
            }
        }
    };
    
    this.play = function() {
        const video = this.container.querySelector('video');
        if (video) video.play();
        const audio = this.container.querySelector('audio');
        if (audio) audio.play();
    };
    
    this.pause = function() {
        const video = this.container.querySelector('video');
        if (video) video.pause();
        const audio = this.container.querySelector('audio');
        if (audio) audio.pause();
    };
    
    this.init();
}
