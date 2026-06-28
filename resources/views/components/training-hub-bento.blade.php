<!-- Whence Training Hub Bento Grid Advert -->
<style>
    .training-hub-container {
        margin: 5px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .training-hub-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 8px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 12px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
    }
    
    .training-hero {
        grid-column: span 2;
        background: white;
        border-radius: 10px;
        padding: 16px;
        position: relative;
        overflow: hidden;
        min-height: 180px;
    }
    
    .training-stats-card {
        border-radius: 10px;
        padding: 14px;
        color: white;
        position: relative;
        overflow: hidden;
        min-height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .training-hero-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }
    
    .training-hero-title {
        font-size: 28px;
        font-weight: 800;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 8px;
        line-height: 1.1;
    }
    
    .training-hero-text {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 12px;
        line-height: 1.4;
        max-width: 580px;
    }
    
    .training-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
        transition: all 0.3s ease;
        transform: translateY(0);
    }
    
    .training-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        color: white;
        text-decoration: none;
    }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .training-hub-container {
            margin: 3px;
        }
        
        .training-hub-grid {
            grid-template-columns: 1fr;
            gap: 6px;
            padding: 10px;
            border-radius: 10px;
        }
        
        .training-hero {
            grid-column: span 1;
            padding: 12px;
            min-height: auto;
        }
        
        .training-hero-title {
            font-size: 22px;
            margin-bottom: 6px;
        }
        
        .training-hero-text {
            font-size: 13px;
            margin-bottom: 10px;
        }
        
        .training-hero-badge {
            font-size: 9px;
            padding: 2px 8px;
            margin-bottom: 6px;
        }
        
        .training-cta-btn {
            padding: 7px 14px;
            font-size: 12px;
            gap: 5px;
        }
        
        .training-stats-card {
            padding: 12px;
            min-height: 100px;
        }
        
        .training-stats-card .stats-icon {
            font-size: 30px !important;
            margin-bottom: 5px !important;
        }
        
        .training-stats-card .stats-number {
            font-size: 26px !important;
            margin-bottom: 2px !important;
        }
        
        .training-stats-card .stats-text {
            font-size: 12px !important;
        }
    }
    
    @media (max-width: 480px) {
        .training-hub-container {
            margin: 2px;
        }
        
        .training-hero {
            padding: 10px;
        }
        
        .training-hero-title {
            font-size: 18px;
        }
        
        .training-hero-text {
            font-size: 12px;
        }
        
        .training-stats-card {
            padding: 10px;
            min-height: 90px;
        }
    }
</style>

<div class="training-hub-container">
    <div class="training-hub-grid">
        
        <!-- Main Hero Section -->
        <div class="training-hero">
            <div style="position: absolute; top: -50px; right: -50px; width: 200px; height: 200px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 50%; opacity: 0.1;"></div>
            <div style="position: absolute; bottom: -30px; left: -30px; width: 150px; height: 150px; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 50%; opacity: 0.1;"></div>
            
            <div style="position: relative; z-index: 2;">
                <div class="training-hero-badge">
                    <i class="fa fa-sparkles" style="margin-right: 6px;"></i>New Learning Experience
                </div>
                
                <h2 class="training-hero-title">
                    Whence Training Hub
                </h2>
                
                <p class="training-hero-text">
                    Level up your skills with interactive courses, video tutorials, and real-time progress tracking. Your professional growth journey starts here.
                </p>
                
                <a href="{{ url('learning') }}" class="training-cta-btn">
                    <span>Start Learning Now</span>
                    <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- Stats Card 1 -->
        <div class="training-stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: white; opacity: 0.1; border-radius: 50%;"></div>
            <div style="position: relative; z-index: 2;">
                <div class="stats-icon" style="font-size: 36px; margin-bottom: 6px;">
                    <i class="fa fa-video"></i>
                </div>
                <div class="stats-number" style="font-size: 28px; font-weight: 800; margin-bottom: 3px;">500+</div>
                <div class="stats-text" style="font-size: 13px; opacity: 0.95; font-weight: 500;">Video Courses Available</div>
            </div>
        </div>

        <!-- Stats Card 2 -->
        <div class="training-stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <div style="position: absolute; top: -20px; right: -20px; width: 100px; height: 100px; background: white; opacity: 0.1; border-radius: 50%;"></div>
            <div style="position: relative; z-index: 2;">
                <div class="stats-icon" style="font-size: 36px; margin-bottom: 6px;">
                    <i class="fa fa-certificate"></i>
                </div>
                <div class="stats-number" style="font-size: 28px; font-weight: 800; margin-bottom: 3px;">100%</div>
                <div class="stats-text" style="font-size: 13px; opacity: 0.95; font-weight: 500;">Certified Training</div>
            </div>
        </div>

    </div>
</div>
