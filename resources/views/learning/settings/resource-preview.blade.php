@extends('layouts.learning')

@section('title', $resourceType . ' Player - Whence Learn')

@section('content')
<!-- Video.js CDN -->
<script type="module" src="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@videojs/html/cdn/video.css" />

<!-- Viewer.js CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/viewerjs@1.11.6/dist/viewer.min.css">
<script src="https://cdn.jsdelivr.net/npm/viewerjs@1.11.6/dist/viewer.min.js"></script>

<style>
/* Full width container - override layout constraints */
.resource-preview-page {
    margin: -24px;
    min-height: calc(100vh - 112px);
    background: var(--light-bg);
}

.resource-preview-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Header */
.preview-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 30px;
    background: white;
    border-bottom: 1px solid var(--border-color);
    margin: -20px -20px 20px -20px;
}

.preview-header a {
    color: var(--primary-color);
    font-size: 20px;
}

.preview-header h1 {
    font-size: 20px;
    font-weight: 600;
    margin: 0;
    flex: 1;
}

/* Resource Card */
.resource-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.resource-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.resource-card-title {
    font-size: 18px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.resource-card-title i {
    font-size: 24px;
}

.resource-card-title.video-title i { color: #FF6B6B; }
.resource-card-title.audio-title i { color: #4ECDC4; }
.resource-card-title.pdf-title i { color: #FF6B6B; }
.resource-card-title.ppt-title i { color: #FFC107; }
.resource-card-title.document-title i { color: #2196F3; }

.resource-card-body {
    padding: 0;
    text-align: center;
    min-height: 500px;
}

/* Video/Audio Player */
.video-player-wrapper,
.audio-player-wrapper {
    width: 100%;
    background: #000;
    padding: 20px;
}

.video-js {
    width: 100%;
    max-height: 70vh;
}

.video-js .vjs-big-play-button {
    background-color: rgba(74, 144, 226, 0.8);
    border: none;
    border-radius: 50%;
    width: 80px;
    height: 80px;
    line-height: 80px;
    font-size: 4em;
}

/* Full Screen Preview */
.full-screen-preview {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: white;
    z-index: 9999;
    display: flex;
    flex-direction: column;
}

.full-screen-preview .exit-button {
    position: absolute;
    top: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    font-size: 24px;
    cursor: pointer;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.full-screen-preview .exit-button:hover {
    background: rgba(0, 0, 0, 0.9);
}

.full-screen-preview .preview-content {
    width: 100%;
    height: 100%;
    padding: 0;
}

/* Disable right-click on preview */
.full-screen-preview {
    -webkit-touch-callout: none;
    -webkit-user-select: none;
    -khtml-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.full-screen-preview iframe {
    pointer-events: none;
}

.full-screen-preview iframe[src*="view.officeapps.live.com"],
.full-screen-preview iframe[src*="docs.google.com"] {
    pointer-events: auto;
}

/* PDF/Document Viewer */
.document-viewer-wrapper {
    padding: 20px;
    min-height: 600px;
}

.document-viewer-wrapper iframe {
    width: 100%;
    height: 70vh;
    border: none;
    border-radius: 8px;
}

/* Loading State */
.preview-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    color: var(--text-secondary);
}

.preview-loading i {
    font-size: 48px;
    margin-bottom: 16px;
    color: var(--primary-color);
}

/* Responsive */
@media (max-width: 768px) {
    .preview-header {
        padding: 16px 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .preview-header h1 {
        font-size: 16px;
        order: 1;
        flex: 1;
        min-width: 200px;
    }
    
    .resource-card-body {
        min-height: 300px;
    }
    
    .video-js {
        max-height: 50vh;
    }
    
    .document-viewer-wrapper iframe {
        height: 50vh;
    }
}
</style>

<div class="resource-preview-page">
    <div class="resource-preview-container">
        <!-- Header -->
        <div class="preview-header">
            <a href="{{ !empty($courseId) ? route('learning.settings.courses') : url()->previous() }}" title="Back">
                <i class="fa fa-arrow-left"></i>
            </a>
            <h1>
                <i class="fa fa-{{ $resourceType === 'Video' ? 'video-camera' : ($resourceType === 'Audio' ? 'music' : ($resourceType === 'PDF' ? 'file-pdf-o' : ($resourceType === 'PPT' ? 'file-powerpoint-o' : 'file-word-o'))) }}"></i>
                {{ $topicName }} - {{ $resourceType }}
            </h1>
        </div>

        <!-- Resource Card -->
        <div class="resource-card">
            <div class="resource-card-header">
                <div class="resource-card-title {{ strtolower($resourceType) }}-title">
                    <i class="fa fa-{{ $resourceType === 'Video' ? 'video-camera' : ($resourceType === 'Audio' ? 'music' : ($resourceType === 'PDF' ? 'file-pdf-o' : ($resourceType === 'PPT' ? 'file-powerpoint-o' : 'file-word-o'))) }}"></i>
                    {{ $resourceType }} Resource
                </div>
            </div>
            
            <div class="resource-card-body" id="resource-card-body">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Pass data from PHP to JavaScript
const resourcePath = '{{ $resourcePath }}';
const resourceType = '{{ $resourceType }}';

// Disable browser download events for document protection
document.addEventListener('DOMContentLoaded', function() {
    // Disable right-click context menu on document
    document.addEventListener('contextmenu', function(e) {
        const target = e.target;
        const isInput = target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.isContentEditable;
        if (!isInput) {
            e.preventDefault();
            return false;
        }
    }, false);

    // Disable keyboard shortcuts for saving/printing
    document.addEventListener('keydown', function(e) {
        // Ctrl+S, Ctrl+P, Ctrl+Shift+S, Ctrl+Shift+P, F12
        if ((e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) ||
            (e.ctrlKey && e.shiftKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) ||
            e.key === 'F12') {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
    }, true);

    // Disable drag and drop download
    document.addEventListener('dragstart', function(e) {
        e.preventDefault();
        return false;
    }, false);

    document.addEventListener('drop', function(e) {
        e.preventDefault();
        return false;
    }, false);

    // Disable Ctrl+U (View Source)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'u') {
            e.preventDefault();
            return false;
        }
    }, true);

    // Load the resource
    loadResource();
});

function loadResource() {
    const container = document.getElementById('resource-card-body');
    
    if (resourceType === 'Video') {
        loadVideoPlayer(container);
    } else if (resourceType === 'Audio') {
        loadAudioPlayer(container);
    } else if (resourceType === 'PDF') {
        loadPDFViewer(container);
    } else if (resourceType === 'PPT' || resourceType === 'Document') {
        loadDocumentViewer(container);
    } else {
        container.innerHTML = `
            <div class="preview-loading">
                <div>
                    <i class="fa fa-exclamation-triangle"></i>
                    <p>Unknown resource type: ${resourceType}</p>
                </div>
            </div>
        `;
    }
}

function loadVideoPlayer(container) {
    // Check if video format is supported
    if (resourcePath.match(/\.(mp4|webm|ogg)$/i)) {
        container.innerHTML = `
            <div class="video-player-wrapper">
                <video id="video-player" class="video-js vjs-big-play-centered" controls preload="auto" data-setup='{"fluid": true, "playbackRates": [0.5, 1, 1.5, 2]}'>
                    <source src="${resourcePath}" type="video/${resourcePath.split('.').pop()}">
                    Your browser does not support HTML5 video.
                </video>
            </div>
        `;
        
        // Initialize Video.js
        const player = videojs('video-player');
        
        // Disable download button
        player.on('loadedmetadata', function() {
            const controlBar = player.controlBar;
            const downloadButton = controlBar.el().querySelector('.vjs-download-button');
            if (downloadButton) {
                downloadButton.style.display = 'none';
            }
        });
    } else {
        // Unsupported format - try to show in full screen
        container.innerHTML = `
            <div style="padding: 60px 20px; text-align: center;">
                <i class="fa fa-video-camera" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
                <h4>Video Format Not Supported</h4>
                <p>This video format cannot be played directly in the browser.</p>
                <button class="btn btn-primary" onclick="openInFullScreen('video', '${resourcePath}')">
                    <i class="fa fa-expand"></i> Try Full Screen
                </button>
            </div>
        `;
    }
}

function loadAudioPlayer(container) {
    // Check if audio format is supported
    if (resourcePath.match(/\.(mp3|wav|ogg)$/i)) {
        container.innerHTML = `
            <div class="audio-player-wrapper" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px;">
                <audio id="audio-player" class="video-js" controls style="width: 100%;" preload="auto">
                    <source src="${resourcePath}" type="audio/${resourcePath.split('.').pop()}">
                    Your browser does not support HTML5 audio.
                </audio>
            </div>
        `;
        
        // Initialize Video.js for audio
        const player = videojs('audio-player');
    } else {
        container.innerHTML = `
            <div style="padding: 60px 20px; text-align: center;">
                <i class="fa fa-music" style="font-size: 64px; color: var(--primary-color); margin-bottom: 20px;"></i>
                <h4>Audio Format Not Supported</h4>
                <p>This audio format cannot be played directly in the browser.</p>
            </div>
        `;
    }
}

function loadPDFViewer(container) {
    container.innerHTML = `
        <div class="document-viewer-wrapper">
            <div style="position: relative; height: 70vh;">
                <iframe 
                    src="https://docs.google.com/gview?url=${encodeURIComponent(resourcePath)}&embedded=true"
                    style="width:100%;height:100%;border:none;"
                    allowfullscreen>
                </iframe>
                
                <div style="position:absolute;top:0;left:0;width:100%;height:45px;z-index:10;background:linear-gradient(to bottom, rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);"></div>
                <div class="download-blocker" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:50;cursor:not-allowed;"></div>
            </div>
        </div>
    `;
}

function loadDocumentViewer(container) {
    container.innerHTML = `
        <div class="document-viewer-wrapper">
            <div style="position: relative; height: 70vh;">
                <iframe 
                    src="https://view.officeapps.live.com/op/view.aspx?src=${encodeURIComponent(resourcePath)}"
                    style="width:100%;height:100%;border:none;"
                    allowfullscreen>
                </iframe>
                
                <div style="position:absolute;top:0;left:0;width:100%;height:45px;z-index:10;background:linear-gradient(to bottom, rgba(255,255,255,1) 0%, rgba(255,255,255,0) 100%);"></div>
                <div class="download-blocker" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:50;cursor:not-allowed;"></div>
            </div>
        </div>
    `;
}

function openInFullScreen(type, filePath) {
    const previewHTML = `
        <div class="full-screen-preview" id="full-screen-preview">
            <button class="exit-button" onclick="exitFullScreen()">
                <i class="fa fa-times"></i>
            </button>
            <div class="preview-content">
                ${type === 'video' ? 
                    `<video id="fullscreen-video" src="${filePath}" controls style="width:100%;height:100%;"></video>` :
                    `<audio id="fullscreen-audio" src="${filePath}" controls style="width:80%;margin:10% auto;"></audio>`
                }
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', previewHTML);
}

function exitFullScreen() {
    const fullScreen = document.getElementById('full-screen-preview');
    if (fullScreen) {
        fullScreen.remove();
    }
}
</script>
@endsection
