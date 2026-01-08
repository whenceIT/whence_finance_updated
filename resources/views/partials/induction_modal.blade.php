<!-- Induction Modal -->
<div id="inductionModal"
    style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,20,0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 999999; display: none; align-items: center; justify-content: center; animation: modalFadeIn 0.4s ease-out; will-change: opacity;">

    <style>
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* @keyframes modalContentAppear {
            from { 
                transform: scale(0.9);
                opacity: 0;
            }
            to { 
                transform: scale(1);
                opacity: 1;
            }
        } */
/* 
        @keyframes slideInUpCustom {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideInRightCustom {
            from {
                transform: translateX(50px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        } */

        /* .animate-slide-up {
            animation: slideInUpCustom 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }

        .animate-slide-right {
            animation: slideInRightCustom 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        } */

        .delay-1 {
            animation-delay: 0.2s;
        }

        .delay-2 {
            animation-delay: 0.5s;
        }

        .delay-3 {
            animation-delay: 0.8s;
        }

        .delay-4 {
            animation-delay: 1.1s;
        }

        .delay-5 {
            animation-delay: 1.4s;
        }

        .wizard-step {
            transition: opacity 0.3s ease;
        }

        @media (max-width: 768px) {
            .wizard-content-flex {
                flex-direction: column !important;
                padding: 20px !important;
                gap: 20px !important;
            }
            .wizard-step-img {
                display: none;
            }
        }
    </style>

    <div
        style="background: white; border-radius: 12px; overflow: hidden; max-width: 900px; width: 50%; box-shadow: 0 20px 50px rgba(0,0,0,0.3); position: relative; border: 1px solid #eee; animation: modalContentAppear 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;">

        <!-- Wizard Header -->
        <div
            style="padding: 10px 15px; border-bottom: 2px solid #ffc107; display: flex; justify-content: space-between; align-items: center; background: white;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <img src="{{ asset('images/w/logo.jpg') }}" alt="Logo"
                    style="height: 45px; width: 45px; border-radius: 8px; object-fit: cover; border: 1px solid #eee;">
                <h3 id="wizardTitle"
                    style="margin: 0; color: #000041; font-weight: 800; font-size: 22px; letter-spacing: -0.5px;">
                    Welcome to Whence Finance!</h3>
            </div>
            <div
                style="font-size: 14px; color: #000041; font-weight: 600; background: #fff9c4; padding: 5px 12px; border-radius: 20px; border: 1px solid #ffecb3;">
                Step <span id="stepNumber">1</span> of 9
            </div>
        </div>

        <!-- Wizard Content -->
        <div id="wizardContent" style="padding: 0; background: white; min-height: 480px;">
            <!-- Step 1: Video -->
            <div id="step1" class="wizard-step" style="display: block;">
                <div style="padding: 35px 40px;">
                    <p style="font-size: 17px; color: #333; margin-bottom: 25px; line-height: 1.6;">
                        <span style="font-size: 24px;">🎉</span> <strong>Welcome aboard!</strong> Before we get started,
                        please take a moment to watch this short induction message from our administration.
                    </p>
                    <div
                        style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 12px; background: #000; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                        <iframe id="inductionVideo"
                            style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                            src="https://drive.google.com/file/d/1zRn-xJCLY6uBWzxVThJJf_YKtmXUw3XY/preview"
                            frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <!-- Step 2: Who We Are -->
            <div id="step2" class="wizard-step" style="display: none;">
                <audio id="step2Audio" src="{{ asset('audio/whoweare.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: center;">
                    <div style="flex: 1;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 32px; margin-top: 0; margin-bottom: 20px;">
                            Who We Are</h2>
                        <p class="animate-slide-up delay-2"
                            style="font-size: 18px; color: #00a04a; font-weight: 700; margin-bottom: 15px; border-left: 4px solid #ffc107; padding-left: 15px;">
                            Whence exists to go Beyond the Familiar.</p>
                        <p class="animate-slide-up delay-2"
                            style="font-size: 16px; color: #444; line-height: 1.8; margin-bottom: 20px;">
                            We are driven by innovation, guided by integrity, and united by a strong belief that
                            meaningful work creates meaningful change.
                        </p>
                        <div class="animate-slide-up delay-3"
                            style="background: #f8f9fa; padding: 20px; border-radius: 12px; border: 1px solid #eee;">
                            <p style="font-size: 15px; color: #333; line-height: 1.7; margin: 0;">
                                <strong>Our mission</strong> is to enhance the well-being of individuals and communities
                                by delivering financial solutions that truly make a difference.
                                Every interaction you have with a client represents our brand, our values, and our
                                promise.
                            </p>
                        </div>
                        <div id="audioStatus" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                        <div id="nextHintStep2" style="margin-top: 10px; font-size: 13px; color: #666; display: none;">
                            <i class="fa fa-info-circle"></i> The Next button will appear once the audio finishes playing.
                        </div>
                    </div>
                    <div style="flex: 1;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/who_we_are.png') }}" alt="Who We Are Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 3: Your Role as a Loan Consultant -->
            <div id="step3" class="wizard-step" style="display: none;">
                <audio id="step3Audio" src="{{ asset('audio/officerrole.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: flex-start;">
                    <div style="flex: 1.2;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 28px; margin-top: 0; margin-bottom: 15px;">
                            Your Role as a Loan Consultant</h2>
                        <p class="animate-slide-up delay-1"
                            style="font-size: 15px; color: #444; line-height: 1.6; margin-bottom: 20px;">
                            As a Loan Consultant, you are on the front line of our impact. You are not just processing
                            loans — you are building relationships, empowering clients, and protecting the integrity of
                            the business.
                        </p>
                        <div class="animate-slide-up delay-2"
                            style="background: #e1f5fe; padding: 20px; border-radius: 12px; border-left: 5px solid #0288d1;">
                            <h4
                                style="margin-top: 0; color: #01579b; font-weight: 800; font-size: 16px; margin-bottom: 12px;">
                                Your role includes:</h4>
                            <ul
                                style="color: #333; margin-bottom: 0; line-height: 1.6; font-size: 14px; list-style-type: none; padding-left: 0;">
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-chevron-circle-right" style="color: #0288d1;"></i> Engaging and
                                    onboarding clients professionally</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-chevron-circle-right" style="color: #0288d1;"></i> Assessing loan
                                    applications responsibly and ethically</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-chevron-circle-right" style="color: #0288d1;"></i> Managing your
                                    loan portfolio with accuracy and discipline</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-chevron-circle-right" style="color: #0288d1;"></i> Upholding
                                    company policies and regulatory standards</li>
                                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-chevron-circle-right" style="color: #0288d1;"></i> Delivering
                                    excellent customer experiences at every touchpoint</li>
                            </ul>
                        </div>
                        <p class="animate-slide-up delay-3"
                            style="font-size: 15px; font-weight: 700; color: #000041; margin-top: 20px; line-height: 1.5;">
                            You are trusted to think, decide, and act in the best interest of both the client and the
                            institution.
                        </p>
                        <div id="audioStatusStep3" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.8;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/loan_consultant_role.png') }}"
                            alt="Loan Consultant Role Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 4: Our Culture: How We Work at Whence -->
            <div id="step4" class="wizard-step" style="display: none;">
                <audio id="step4Audio" src="{{ asset('audio/ourculture.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: flex-start;">
                    <div style="flex: 1.3;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 28px; margin-top: 0; margin-bottom: 12px;">
                            Our Culture: How We Work at Whence</h2>
                        <p class="animate-slide-up delay-1"
                            style="font-size: 15px; color: #444; line-height: 1.6; margin-bottom: 15px;">
                            At Whence, culture is not a slogan — it is how we show up every day.
                        </p>
                        <div class="animate-slide-up delay-2"
                            style="background: #f0f4c3; padding: 20px; border-radius: 12px; border-top: 5px solid #afb42b;">
                            <h4
                                style="margin-top: 0; color: #33691e; font-weight: 800; font-size: 16px; margin-bottom: 12px;">
                                We value:</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div style="font-size: 13px; color: #333;">
                                    <strong style="color: #000041; display: block;">Ownership</strong> You take
                                    responsibility for your work and outcomes
                                </div>
                                <div style="font-size: 13px; color: #333;">
                                    <strong style="color: #000041; display: block;">Integrity</strong> You do the right
                                    thing, even when no one is watching
                                </div>
                                <div style="font-size: 13px; color: #333;">
                                    <strong style="color: #000041; display: block;">Courage</strong> You speak up, ask
                                    questions, and challenge ideas respectfully
                                </div>
                                <div style="font-size: 13px; color: #333;">
                                    <strong style="color: #000041; display: block;">Collaboration</strong> You support
                                    your teammates and share knowledge
                                </div>
                                <div style="font-size: 13px; color: #333;">
                                    <strong style="color: #000041; display: block;">Excellence</strong> You strive to
                                    improve continuously
                                </div>
                            </div>
                        </div>
                        <div class="animate-slide-up delay-4"
                            style="margin-top: 20px; background: #fafafa; padding: 15px; border-radius: 10px; border: 1px dashed #ccc;">
                            <p style="font-size: 14px; color: #333; line-height: 1.6; margin: 0;">
                                We operate on <strong>freedom with responsibility</strong>. You are empowered to make
                                decisions, but also accountable for results.
                            </p>
                        </div>
                        <div id="audioStatusStep4" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.7;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/our_culture.png') }}" alt="Our Culture Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 5: What Success Looks Like -->
            <div id="step5" class="wizard-step" style="display: none;">
                <audio id="step5Audio" src="{{ asset('audio/success.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: flex-start;">
                    <div style="flex: 1.2;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 28px; margin-top: 0; margin-bottom: 12px;">
                            What Success Looks Like</h2>
                        <p class="animate-slide-up delay-1"
                            style="font-size: 15px; color: #444; line-height: 1.6; margin-bottom: 15px;">
                            Success at Whence is not measured only by numbers, but by how you:
                        </p>
                        <div class="animate-slide-up delay-2"
                            style="background: #fff8e1; padding: 20px; border-radius: 12px; border-left: 5px solid #ffb300;">
                            <ul
                                style="color: #333; margin-bottom: 0; line-height: 1.8; font-size: 14px; list-style-type: none; padding-left: 0;">
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-star" style="color: #ffb300;"></i> Build trust with clients</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-shield-alt" style="color: #ffb300;"></i> Protect the company’s
                                    reputation</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-users" style="color: #ffb300;"></i> Work with your team</li>
                                <li style="margin-bottom: 8px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-lightbulb" style="color: #ffb300;"></i> Learn, grow, and adapt
                                </li>
                                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-gem" style="color: #ffb300;"></i> Uphold our values even under
                                    pressure</li>
                            </ul>
                        </div>
                        <p class="animate-slide-up delay-4"
                            style="font-size: 14px; color: #00a04a; font-weight: 700; margin-top: 20px; background: white; border: 1px solid #00a04a; padding: 10px 15px; border-radius: 8px; display: inline-block;">
                            We believe in high performance, honest feedback, and continuous improvement.
                        </p>
                        <div id="audioStatusStep5" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.8;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/what_success.png') }}" alt="What Success Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 6: Dress Code -->
            <div id="step6" class="wizard-step" style="display: none;">
                <audio id="step6Audio" src="{{ asset('audio/dresscode.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 35px; gap: 35px; align-items: flex-start;">
                    <div style="flex: 1.4;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 26px; margin-top: 0; margin-bottom: 10px;">
                            DRESS CODE</h2>
                        <p class="animate-slide-up delay-1"
                            style="font-size: 14px; color: #444; line-height: 1.5; margin-bottom: 12px;">
                            Your appearance reflects not only you, but the entire Whence brand. We therefore
                            expect the following standards to be observed at all times:
                        </p>
                        <div class="animate-slide-up delay-2"
                            style="background: #f1f8e9; padding: 15px 20px; border-radius: 10px; border-top: 4px solid #00a04a; max-height: 300px; overflow-y: auto; scrollbar-width: thin;">
                            <h4
                                style="margin-top: 0; color: #2e7d32; font-weight: 800; font-size: 15px; margin-bottom: 10px;">
                                Attire Standards:</h4>
                            <ul
                                style="color: #333; margin-bottom: 0; line-height: 1.5; font-size: 13px; list-style-type: none; padding-left: 0;">
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Dress
                                    professionally with clean, neat, and well-pressed clothing</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Male staff are
                                    required to wear neckties unless in official Whence attire</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Clothing
                                    should not be transparent, revealing, or excessively casual</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Hair must be
                                    neat and well-groomed</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Maintain high
                                    standards of personal hygiene</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Whence-branded
                                    attire may be worn on Fridays and during field assignments</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-check" style="color: #00a04a; margin-top: 3px;"></i> Open-toed
                                    shoes are not permitted except approved corporate heels</li>
                                <li style="margin-bottom: 6px; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-times" style="color: #d32f2f; margin-top: 3px;"></i> Clothing with
                                    offensive wording or images is not acceptable</li>
                                <li style="margin-bottom: 0; display: flex; align-items: flex-start; gap: 8px;"><i
                                        class="fa fa-times" style="color: #d32f2f; margin-top: 3px;"></i> Outfits
                                    exposing the waist, back, or shoulders are not permitted</li>
                            </ul>
                        </div>
                        <p class="animate-slide-up delay-5"
                            style="font-size: 12px; font-style: italic; color: #666; margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px;">
                            Reasonable accommodations can be made for religious, cultural, or medical reasons upon
                            engagement with management.
                        </p>
                        <div id="audioStatusStep6" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.6;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/dress_code.png') }}" alt="Dress Code Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 7: Office Etiquette -->
            <div id="step7" class="wizard-step" style="display: none;">
                <audio id="step7Audio" src="{{ asset('audio/enticate.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: flex-start;">
                    <div style="flex: 1.2;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 28px; margin-top: 0; margin-bottom: 12px;">
                            OFFICE ETIQUETTE</h2>
                        <p class="animate-slide-up delay-1"
                            style="font-size: 15px; color: #444; line-height: 1.6; margin-bottom: 15px;">
                            Professional conduct is essential to our working environment. You are expected to:
                        </p>
                        <div class="animate-slide-up delay-2"
                            style="background: #e3f2fd; padding: 25px; border-radius: 12px; border-left: 5px solid #1976d2;">
                            <ul
                                style="color: #333; margin-bottom: 0; line-height: 1.8; font-size: 14px; list-style-type: none; padding-left: 0;">
                                <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-clock" style="color: #1976d2;"></i> Report to work on time (08:00hrs)
                                </li>
                                <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-smile" style="color: #1976d2;"></i> Maintain a calm, respectful
                                    environment</li>
                                <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-briefcase" style="color: #1976d2;"></i> Keep your workstation neat and
                                    organized</li>
                                <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-comments" style="color: #1976d2;"></i> Use respectful language at all
                                    times</li>
                                <li style="margin-bottom: 10px; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-phone-slash" style="color: #1976d2;"></i> Take personal or sensitive
                                    calls privately</li>
                                <li style="margin-bottom: 0; display: flex; align-items: center; gap: 10px;"><i
                                        class="fa fa-user-tie" style="color: #1976d2;"></i> Demonstrate professionalism in
                                    all interactions</li>
                            </ul>
                        </div>
                        <p class="animate-slide-up delay-4"
                            style="font-size: 14px; color: #1565c0; font-weight: 700; margin-top: 20px;">
                            A calm and respectful environment is mandatory, especially when clients are present.
                        </p>
                        <div id="audioStatusStep7" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.8;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/dress_code.png') }}" alt="Office Etiquette Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 8: A Final Word -->
            <div id="step8" class="wizard-step" style="display: none;">
                <audio id="step8Audio" src="{{ asset('audio/finalword.mp3') }}" preload="auto"></audio>
                <div class="wizard-content-flex" style="display: flex; flex-direction: row; padding: 40px; gap: 40px; align-items: center;">
                    <div style="flex: 1.2;">
                        <h2 class="animate-slide-up delay-1"
                            style="color: #000041; font-weight: 900; font-size: 32px; margin-top: 0; margin-bottom: 20px;">
                            A Final Word</h2>
                        <p class="animate-slide-up delay-2"
                            style="font-size: 16px; color: #444; line-height: 1.8; margin-bottom: 20px;">
                            At Whence, we believe great people build great institutions. We are excited to have you on
                            board and confident that your contribution will help us continue to grow, innovate, and make
                            a meaningful impact.
                        </p>
                        <div class="animate-slide-up delay-3"
                            style="background: #e8f5e9; padding: 25px; border-radius: 12px; border-left: 5px solid #00a04a;">
                            <p style="font-size: 18px; color: #000041; font-weight: 700; line-height: 1.6; margin: 0;">
                                Welcome to the team.<br>
                                Welcome to Whence.<br>
                                Welcome Beyond the Familiar.
                            </p>
                        </div>
                        <div id="audioStatusStep8" style="margin-top: 20px; font-size: 14px; color: #000041; display: none; align-items: center; gap: 10px;">
                            <i class="fa fa-volume-up"></i> <span>Audio introduction playing... The Next button will appear once the audio finishes.</span>
                        </div>
                    </div>
                    <div style="flex: 0.8;" class="animate-slide-right delay-2 wizard-step-img">
                        <img src="{{ asset('images/induction/who_we_are.png') }}" alt="Final Word Illustration"
                            style="width: 100%; height: auto; border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1);">
                    </div>
                </div>
            </div>

            <!-- Step 9: Confirmation -->
            <div id="step9" class="wizard-step" style="display: none;">
                <div style="padding: 50px 40px; text-align: center;">
                    <div style="font-size: 70px; color: #00a04a; margin-bottom: 25px;">
                        <i class="fa fa-check-circle" style="filter: drop-shadow(0 4px 8px rgba(0,160,74,0.2));"></i>
                    </div>
                    <h2 style="color: #000041; margin-bottom: 20px; font-weight: 800; font-size: 28px;">You're ready
                        to
                        go!</h2>
                    <p style="font-size: 18px; color: #444; line-height: 1.7; max-width: 650px; margin: 0 auto 35px;">
                        By clicking <strong>Finish</strong>, you acknowledge that you have watched the induction
                        video and are ready to explore the system features.
                    </p>
                    <div
                        style="background: #f1f8e9; padding: 25px; border-radius: 12px; border-left: 6px solid #00a04a; text-align: left; max-width: 650px; margin: 0 auto; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        <h4
                            style="margin-top: 0; color: #1b5e20; font-weight: 800; display: flex; align-items: center; gap: 10px;">
                            <i class="fa fa-rocket"></i> Next Steps:
                        </h4>
                        <table style="color: #333; margin-bottom: 0; line-height: 1.8; font-size: 15px; width: 100%; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 5px 10px;"><input type="checkbox" disabled style="margin-right: 10px;"></td>
                                <td style="padding: 5px 10px;">Review and sign pending company policies.</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 10px;"><input type="checkbox" disabled style="margin-right: 10px;"></td>
                                <td style="padding: 5px 10px;">Complete training on Cash Handling in the Loan Management System.</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 10px;"><input type="checkbox" disabled style="margin-right: 10px;"></td>
                                <td style="padding: 5px 10px;">Explore your personal dashboard and tools.</td>
                            </tr>
                            <tr>
                                <td style="padding: 5px 10px;"><input type="checkbox" disabled style="margin-right: 10px;"></td>
                                <td style="padding: 5px 10px;">Reach out to your supervisor for any guidance.</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wizard Footer -->
        <div
            style="padding: 8px 15px; border-top: 1px solid #eee; display: flex; justify-content: flex-end; gap: 12px; background: #fafafa;">
            <button id="btnPrev"
                style="display: none; padding: 8px 20px; border: 2px solid #ddd; background: white; border-radius: 6px; cursor: pointer; font-weight: 600; color: #555; transition: all 0.2s ease; font-size: 14px;">
                <i class="fa fa-arrow-left" style="margin-right: 6px;"></i> Previous
            </button>
            <button id="btnNext"
                style="padding: 8px 24px; border: none; background: #00a04a; color: white; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">
                Next <i class="fa fa-arrow-right" style="margin-left: 6px;"></i>
            </button>
            <button id="btnFinish"
                style="display: none; padding: 8px 24px; border: none; background: #000041; color: white; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 14px; transition: all 0.2s ease;">
                Review company policies <i class="fa fa-check" style="margin-left: 6px;"></i>
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var currentStep = parseInt(localStorage.getItem('inductionCurrentStep') || '1');
    var totalSteps = 9;
    var btnNext = document.getElementById('btnNext');
    var btnPrev = document.getElementById('btnPrev');
    var btnFinish = document.getElementById('btnFinish');
    var stepNumber = document.getElementById('stepNumber');
    var wizardTitle = document.getElementById('wizardTitle');
    var step2Audio = document.getElementById('step2Audio');
    var audioStatus = document.getElementById('audioStatus');
    var step3Audio = document.getElementById('step3Audio');
    var audioStatusStep3 = document.getElementById('audioStatusStep3');
    var step4Audio = document.getElementById('step4Audio');
    var audioStatusStep4 = document.getElementById('audioStatusStep4');
    var step5Audio = document.getElementById('step5Audio');
    var audioStatusStep5 = document.getElementById('audioStatusStep5');
    var step6Audio = document.getElementById('step6Audio');
    var audioStatusStep6 = document.getElementById('audioStatusStep6');
    var step7Audio = document.getElementById('step7Audio');
    var audioStatusStep7 = document.getElementById('audioStatusStep7');
    var step8Audio = document.getElementById('step8Audio');
    var audioStatusStep8 = document.getElementById('audioStatusStep8');
    var completedSteps = new Set(JSON.parse(localStorage.getItem('inductionCompletedSteps') || '[]'));

    function saveCurrentStep() {
        localStorage.setItem('inductionCurrentStep', currentStep);
    }

        step2Audio.addEventListener('ended', function () {
            if (currentStep === 2) {
                btnNext.style.display = 'block';
                if (audioStatus) audioStatus.style.display = 'none';
                completedSteps.add(2);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step2Audio.addEventListener('play', function () {
            if (audioStatus) audioStatus.style.display = 'flex';
        });

        step3Audio.addEventListener('ended', function () {
            if (currentStep === 3) {
                btnNext.style.display = 'block';
                if (audioStatusStep3) audioStatusStep3.style.display = 'none';
                completedSteps.add(3);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step3Audio.addEventListener('play', function () {
            if (audioStatusStep3) audioStatusStep3.style.display = 'flex';
        });

        step4Audio.addEventListener('ended', function () {
            if (currentStep === 4) {
                btnNext.style.display = 'block';
                if (audioStatusStep4) audioStatusStep4.style.display = 'none';
                completedSteps.add(4);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step4Audio.addEventListener('play', function () {
            if (audioStatusStep4) audioStatusStep4.style.display = 'flex';
        });

        step5Audio.addEventListener('ended', function () {
            if (currentStep === 5) {
                btnNext.style.display = 'block';
                if (audioStatusStep5) audioStatusStep5.style.display = 'none';
                completedSteps.add(5);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step5Audio.addEventListener('play', function () {
            if (audioStatusStep5) audioStatusStep5.style.display = 'flex';
        });

        step6Audio.addEventListener('ended', function () {
            if (currentStep === 6) {
                btnNext.style.display = 'block';
                if (audioStatusStep6) audioStatusStep6.style.display = 'none';
                completedSteps.add(6);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step6Audio.addEventListener('play', function () {
            if (audioStatusStep6) audioStatusStep6.style.display = 'flex';
        });

        step7Audio.addEventListener('ended', function () {
            if (currentStep === 7) {
                btnNext.style.display = 'block';
                if (audioStatusStep7) audioStatusStep7.style.display = 'none';
                completedSteps.add(7);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step7Audio.addEventListener('play', function () {
            if (audioStatusStep7) audioStatusStep7.style.display = 'flex';
        });

        step8Audio.addEventListener('ended', function () {
            if (currentStep === 8) {
                btnNext.style.display = 'block';
                if (audioStatusStep8) audioStatusStep8.style.display = 'none';
                completedSteps.add(8);
                localStorage.setItem('inductionCompletedSteps', JSON.stringify([...completedSteps]));
            }
        });

        step8Audio.addEventListener('play', function () {
            if (audioStatusStep8) audioStatusStep8.style.display = 'flex';
        });

        btnNext.addEventListener('click', function () {
            if (currentStep < totalSteps) {
                currentStep++;
                updateWizard();
            }
        });

        btnPrev.addEventListener('click', function () {
            if (currentStep > 1) {
                currentStep--;
                updateWizard();
            }
        });

        function updateWizard() {
            // Pause step 2 audio if moving away
            if (currentStep !== 2 && step2Audio) {
                step2Audio.pause();
                if (audioStatus) audioStatus.style.display = 'none';
            }

            // Pause step 3 audio if moving away
            if (currentStep !== 3 && step3Audio) {
                step3Audio.pause();
                if (audioStatusStep3) audioStatusStep3.style.display = 'none';
            }

            // Pause step 4 audio if moving away
            if (currentStep !== 4 && step4Audio) {
                step4Audio.pause();
                if (audioStatusStep4) audioStatusStep4.style.display = 'none';
            }

            // Pause step 5 audio if moving away
            if (currentStep !== 5 && step5Audio) {
                step5Audio.pause();
                if (audioStatusStep5) audioStatusStep5.style.display = 'none';
            }

            // Pause step 6 audio if moving away
            if (currentStep !== 6 && step6Audio) {
                step6Audio.pause();
                if (audioStatusStep6) audioStatusStep6.style.display = 'none';
            }

            // Pause step 7 audio if moving away
            if (currentStep !== 7 && step7Audio) {
                step7Audio.pause();
                if (audioStatusStep7) audioStatusStep7.style.display = 'none';
            }

            // Pause step 8 audio if moving away
            if (currentStep !== 8 && step8Audio) {
                step8Audio.pause();
                if (audioStatusStep8) audioStatusStep8.style.display = 'none';
            }

            // Hide ALL steps first to prevent distortion/stacking
            document.querySelectorAll('.wizard-step').forEach(step => {
                step.style.display = 'none';
            });

            // Show current step
            document.getElementById('step' + currentStep).style.display = 'block';

            // Trigger animations for Steps 2-8
            if (currentStep >= 2 && currentStep <= 8) {
                const animatedElements = document.querySelectorAll('#step' + currentStep + ' [class*="animate-"]');
                animatedElements.forEach(el => {
                    el.style.animation = 'none';
                    el.offsetHeight; // trigger reflow
                    el.style.animation = null;
                });
            }

            // Update Header & Buttons
            stepNumber.textContent = currentStep;

            if (currentStep === 1) {
                wizardTitle.textContent = 'Welcome to Whence Finance!';
                btnPrev.style.display = 'none';
                btnNext.style.display = 'block';
                btnFinish.style.display = 'none';
            } else if (currentStep === 2) {
                wizardTitle.textContent = 'Who We Are';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(2)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step2Audio) {
                        step2Audio.currentTime = 0;
                        step2Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 3) {
                wizardTitle.textContent = 'Your Role as a Loan Consultant';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(3)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step3Audio) {
                        step3Audio.currentTime = 0;
                        step3Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 4) {
                wizardTitle.textContent = 'Our Culture';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(4)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step4Audio) {
                        step4Audio.currentTime = 0;
                        step4Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 5) {
                wizardTitle.textContent = 'What Success Looks Like';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(5)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step5Audio) {
                        step5Audio.currentTime = 0;
                        step5Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 6) {
                wizardTitle.textContent = 'Dress Code';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(6)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step6Audio) {
                        step6Audio.currentTime = 0;
                        step6Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 7) {
                wizardTitle.textContent = 'Office Etiquette';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(7)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step7Audio) {
                        step7Audio.currentTime = 0;
                        step7Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else if (currentStep === 8) {
                wizardTitle.textContent = 'A Final Word';
                btnPrev.style.display = 'block';
                btnFinish.style.display = 'none';

                if (completedSteps.has(8)) {
                    btnNext.style.display = 'block';
                } else {
                    btnNext.style.display = 'none'; // Hide until audio ends

                    if (step8Audio) {
                        step8Audio.currentTime = 0;
                        step8Audio.play().catch(function(error) {
                            console.log("Audio play failed:", error);
                            // Fallback: show button if audio can't play
                            btnNext.style.display = 'block';
                        });
                    }
                }
            } else { // currentStep === 9
                wizardTitle.textContent = 'Induction Final Step';
                btnPrev.style.display = 'block';
                btnNext.style.display = 'none';
                btnFinish.style.display = 'block';
            }

            saveCurrentStep();
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
                            window.location.href = "{{ route('policies.view_policies') }}";
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

        // Show modal after 3 seconds
        setTimeout(function() {
            var modal = document.getElementById('inductionModal');
            if (modal) {
                modal.style.display = 'flex';
            }
            updateWizard();
        }, 5000);
    })();
</script>