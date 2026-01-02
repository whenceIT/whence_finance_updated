<!-- Induction Modal -->
<div id="inductionModal"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 999999; display: flex; align-items: center; justify-content: center; animation: modalFadeIn 0.5s ease-out;">
    <div
        style="background: white; border-radius: 12px; overflow: hidden; max-width: 900px; width: 95%; box-shadow: 0 20px 50px rgba(0,0,0,0.5); position: relative;">

        <!-- Wizard Header -->
        <div
            style="padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #f9f9f9;">
            <h3 id="wizardTitle" style="margin: 0; color: #000041; font-weight: bold;">Welcome to Whence
                Finance!</h3>
            <div style="font-size: 14px; color: #666;">Step <span id="stepNumber">1</span> of 2</div>
        </div>

        <!-- Wizard Content -->
        <div id="wizardContent" style="padding: 0;">
            <!-- Step 1: Video -->
            <div id="step1" style="display: block;">
                <div style="padding: 25px;">
                    <p style="font-size: 16px; color: #444; margin-bottom: 20px;">You’re officially on board! 🎉 Before anything else, 
                        let’s start with a short message from The Administration, bringing everything into focus.</p>
                    <div
                        style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 8px; background: #000; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
                        <iframe id="inductionVideo"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                            src="https://www.youtube.com/embed/dQw4w9WgXcQ?rel=0" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- Step 2: Confirmation -->
            <div id="step2" style="display: none;">
                <div style="padding: 40px 30px; text-align: center;">
                    <div style="font-size: 60px; color: #00a04a; margin-bottom: 20px;"><i class="fa fa-info-circle"></i>
                    </div>
                    <h2 style="color: #000041; margin-bottom: 15px;">You're ready to go!</h2>
                    <p style="font-size: 18px; color: #555; line-height: 1.6; max-width: 600px; margin: 0 auto 30px;">
                        By clicking finish, you acknowledge that you've watched the induction video and
                        are ready to explore the system features.
                    </p>
                    <div
                        style="background: #e8f5e9; padding: 20px; border-radius: 8px; border-left: 5px solid #00a04a; text-align: left; max-width: 600px; margin: 0 auto;">
                        <h4 style="margin-top: 0; color: #2e7d32; font-weight: bold;">Next Steps:</h4>
                        <ul style="color: #444; margin-bottom: 0;">
                            <li>You might be asked to review and sign company policies.</li>
                            <li>Explore your dashboard tools on the left menu.</li>
                            <li>Contact your supervisor if you have any questions.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wizard Footer -->
        <div style="padding: 20px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 10px;">
            <button id="btnPrev"
                style="display: none; padding: 10px 25px; border: 1px solid #ccc; background: white; border-radius: 6px; cursor: pointer; font-weight: bold; color: #666; transition: all 0.3s;">Previous</button>
            <button id="btnNext"
                style="padding: 10px 25px; border: none; background: #00a04a; color: white; border-radius: 6px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 10px rgba(0,160,74,0.3); transition: all 0.3s;">Next</button>
            <button id="btnFinish"
                style="display: none; padding: 10px 25px; border: none; background: #000041; color: white; border-radius: 6px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 10px rgba(0,0,65,0.3); transition: all 0.3s;">Finish</button>
        </div>
    </div>
</div>

<script>
    (function () {
        var currentStep = 1;
        var totalSteps = 2;
        var btnNext = document.getElementById('btnNext');
        var btnPrev = document.getElementById('btnPrev');
        var btnFinish = document.getElementById('btnFinish');
        var step1 = document.getElementById('step1');
        var step2 = document.getElementById('step2');
        var stepNumber = document.getElementById('stepNumber');
        var wizardTitle = document.getElementById('wizardTitle');

        btnNext.addEventListener('click', function () {
            currentStep = 2;
            updateWizard();
        });

        btnPrev.addEventListener('click', function () {
            currentStep = 1;
            updateWizard();
        });

        function updateWizard() {
            if (currentStep === 1) {
                step1.style.display = 'block';
                step2.style.display = 'none';
                btnPrev.style.display = 'none';
                btnNext.style.display = 'block';
                btnFinish.style.display = 'none';
                stepNumber.textContent = '1';
                wizardTitle.textContent = 'Welcome to Whence Finance!';
            } else {
                step1.style.display = 'none';
                step2.style.display = 'block';
                btnPrev.style.display = 'block';
                btnNext.style.display = 'none';
                btnFinish.style.display = 'block';
                stepNumber.textContent = '2';
                wizardTitle.textContent = 'Induction Final Step';
            }
        }

        btnFinish.addEventListener('click', function () {
            btnFinish.disabled = true;
            btnFinish.textContent = 'Saving...';

            fetch('{{ url("induction/mark_as_seen") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('inductionModal').style.opacity = '0';
                        setTimeout(function () {
                            location.reload(); // Reload to show policy modal if needed
                        }, 500);
                    } else {
                        alert('Error saving induction status. Please try again.');
                        btnFinish.disabled = false;
                        btnFinish.textContent = 'Finish';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Connection error. Please try again.');
                    btnFinish.disabled = false;
                    btnFinish.textContent = 'Finish';
                });
        });
    })();
</script>