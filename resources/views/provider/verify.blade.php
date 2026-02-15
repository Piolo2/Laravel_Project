@extends('layouts.app')

@section('title', 'Verify Account | Uni-Serve')

@push('styles')
    <style>
        .stepper-wrapper {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            position: relative;
        }

        .stepper-item {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
        }

        .stepper-item::before {
            position: absolute;
            content: "";
            border-bottom: 3px solid #e0e0e0;
            width: 100%;
            top: 20px;
            left: -50%;
            z-index: 1;
        }

        .stepper-item::after {
            position: absolute;
            content: "";
            border-bottom: 3px solid #e0e0e0;
            width: 100%;
            top: 20px;
            left: 50%;
            z-index: 1;
        }

        .stepper-item .step-counter {
            position: relative;
            z-index: 5;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #fff;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .stepper-item .step-name {
            margin-top: 5px;
            font-size: 0.85rem;
            color: #666;
            font-weight: 600;
        }

        .stepper-item.active .step-counter {
            background-color: var(--primary-color, #007bff);
            transform: scale(1.1);
        }

        .stepper-item.completed .step-counter {
            background-color: var(--primary-color, #007bff);
        }

        .stepper-item.completed::after {
            border-bottom: 3px solid var(--primary-color, #007bff);
        }

        .stepper-item.completed+.stepper-item::before {
            border-bottom: 3px solid var(--primary-color, #007bff);
        }

        .stepper-item.first::before {
            content: none;
        }

        .stepper-item.last::after {
            content: none;
        }

        /* Mobile Responsive Stepper */
        @media (max-width: 768px) {
            .stepper-item .step-counter {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .stepper-item .step-name {
                font-size: 0.7rem;
                margin-top: 2px;
            }

            .stepper-item::before,
            .stepper-item::after {
                top: 15px;
                /* Center of 30px is 15px */
                border-bottom-width: 2px;
                /* Thinner line */
            }
        }
    </style>
@endpush

@section('content')
    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-12">
                <div class="card shadow-sm" style="border: none; border-radius: 15px; overflow: hidden;">
                    <div class="card-header bg-white border-bottom p-3 p-md-4">
                        <h2 class="h3 h2-md mb-4 fw-bold text-center" style="color: var(--primary-color, #007bff);">Verify
                            an Account</h2>

                        <!-- Stepper -->
                        <div class="stepper-wrapper">
                            <div class="stepper-item first active" id="stepper1">
                                <div class="step-counter">1</div>
                                <div class="step-name">Personal</div>
                            </div>
                            <div class="stepper-item" id="stepper2">
                                <div class="step-counter">2</div>
                                <div class="step-name">Professional</div>
                            </div>
                            <div class="stepper-item last" id="stepper3">
                                <div class="step-counter">3</div>
                                <div class="step-name">Documents</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-3 p-md-5">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="verificationForm" action="{{ route('verification.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <!-- Step 1: Personal Information -->
                            <div class="form-step active" id="step1">
                                <h4 class="mb-4" style="color: #555;">Personal Information</h4>

                                <div class="row">

                                    <div class="col-md-6 mb-3">
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control" required
                                            value="{{ old('first_name', $user->profile?->full_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control" required
                                            value="{{ old('last_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="middle_name">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" class="form-control"
                                            value="{{ old('middle_name') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_number">Contact No. <span class="text-danger">*</span></label>
                                        <input type="text" name="contact_number" id="contact_number" class="form-control" required
                                            value="{{ old('contact_number', $user->profile?->contact_number) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="age">Age</label>
                                        <input type="number" name="age" id="age" class="form-control" value="{{ old('age') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control">
                                            <option value="">Select gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="text-right mt-4" style="text-align: right;">
                                    <button type="button" class="btn btn-primary next-step"
                                        style="padding: 10px 30px; border-radius: 25px;">Next &rarr;</button>
                                </div>
                            </div>

                            <!-- Step 2: Professional Information -->
                            <div class="form-step" id="step2" style="display: none;">
                                <h4 class="mb-4" style="color: #555;">Professional Information</h4>

                                <div class="mb-3">
                                    <label for="years_experience">No. of Years Experience</label>
                                    <select name="years_experience" id="years_experience" class="form-control">
                                        <option value="0">Less than 1 year</option>
                                        <option value="1">1-2 years</option>
                                        <option value="3">3-5 years</option>
                                        <option value="5">5+ years</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2">Type of Skills <span class="text-danger">*</span></div>
                                    <!-- Basic multi-select or checkboxes -->
                                    <div class="p-2 border rounded" style="max-height: 150px; overflow-y: auto;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skill_types[]"
                                                value="Plumbing" id="skill1">
                                            <label class="form-check-label" for="skill1">Plumbing</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skill_types[]"
                                                value="Electrical" id="skill2">
                                            <label class="form-check-label" for="skill2">Electrical</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skill_types[]"
                                                value="Cleaning" id="skill3">
                                            <label class="form-check-label" for="skill3">Cleaning</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skill_types[]"
                                                value="Carpentry" id="skill4">
                                            <label class="form-check-label" for="skill4">Carpentry</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skill_types[]"
                                                value="Massage" id="skill5">
                                            <label class="form-check-label" for="skill5">Massage</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="service_type">Type of Service</label>
                                    <select name="service_type" id="service_type" class="form-control">
                                        <option value="Home Service">Home Service</option>
                                        <option value="Shop Based">Shop Based</option>
                                        <option value="Remote">Remote</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="address">Complete Address <span class="text-danger">*</span></label>
                                    <input type="text" name="address" id="address" class="form-control" required
                                        value="{{ old('address', $user->profile?->address) }}">
                                </div>

                                <div class="mb-3">
                                    <label for="civil_status">Civil Status</label>
                                    <select name="civil_status" id="civil_status" class="form-control">
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widowed">Widowed</option>
                                        <option value="Separated">Separated</option>
                                    </select>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary prev-step"
                                        style="background: white; color: #666; border: none;">Back</button>
                                    <button type="button" class="btn btn-primary next-step"
                                        style="padding: 10px 30px; border-radius: 25px;">Next &rarr;</button>
                                </div>
                            </div>

                            <!-- Step 3: Documents & Privacy -->
                            <div class="form-step" id="step3" style="display: none;">
                                <h4 class="mb-4" style="color: #555;">Verification Documents</h4>

                                <div class="mb-3">
                                    <label for="education_attainment">Educational Attainment</label>
                                    <select name="education_attainment" id="education_attainment" class="form-control">
                                        <option value="High School">High School</option>
                                        <option value="College Undergraduate">College Undergraduate</option>
                                        <option value="College Graduate">College Graduate</option>
                                        <option value="Vocational">Vocational</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="work_status">Work Status</label>
                                    <select name="work_status" id="work_status" class="form-control">
                                        <option value="Full-Time">Full-Time</option>
                                        <option value="Part-Time">Part-Time</option>
                                        <option value="Freelance">Freelance</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="hasCert">Do you have any Skills Certification? (YES/NO)</label>
                                    <select id="hasCert" name="has_compliance_certificates" class="form-control"
                                        onchange="toggleCertUpload()">
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="certUpload" style="display: none;">
                                    <label for="compliance_certificate_file">Upload proof of skills (Certificates, NC II, Training Records)</label>
                                    <input type="file" name="compliance_certificate_file" id="compliance_certificate_file" class="form-control">
                                </div>

                                <hr class="my-4">

                                <h5 style="color: #555;">Valid ID</h5>
                                <div class="mb-3">
                                    <label for="id_type">Type of ID</label>
                                    <select name="id_type" id="id_type" class="form-control">
                                        <option value="Passport">Passport</option>
                                        <option value="Driver's License">Driver's License</option>
                                        <option value="UMID">UMID</option>
                                        <option value="SSS">SSS</option>
                                        <option value="National ID">National ID</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="id_front_file">Upload ID (Front) <span class="text-danger">*</span></label>
                                        <input type="file" name="id_front_file" id="id_front_file" class="form-control" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="id_back_file">Upload ID (Back) <span class="text-danger">*</span></label>
                                        <input type="file" name="id_back_file" id="id_back_file" class="form-control" required>
                                    </div>
                                </div>

                                <div class="privacy-note mt-4 p-3"
                                    style="background: #f9f9f9; border-radius: 8px; font-size: 0.9em; color: #666;">
                                    <p><strong>By providing my information, I confirm that all details are true and correct
                                            and consent to the use of my data for verification purposes.</strong></p>
                                    <p>Your information will be used only to verify your identity, qualifications, and
                                        skills. All data is securely handled and accessed only by authorized personnel.</p>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" id="confirmData" required>
                                        <label class="form-check-label" for="confirmData">Confirm my information</label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-secondary prev-step"
                                        style="background: white; color: #666; border: none;">Back</button>
                                    <button type="submit" class="btn btn-primary"
                                        style="padding: 10px 30px; border-radius: 25px; width: 200px;">Verify my
                                        account</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            let currentStep = 1;
            const steps = [1, 2, 3];

            function showStep(step) {
                // Show Form Step
                steps.forEach(s => {
                    const el = document.getElementById('step' + s);
                    if (el) el.style.display = (s === step) ? 'block' : 'none';
                });

                // Update Stepper UI
                steps.forEach(s => {
                    const stepperEl = document.getElementById('stepper' + s);
                    if (stepperEl) {
                        stepperEl.classList.remove('active', 'completed');
                        if (s < step) {
                            stepperEl.classList.add('completed');
                        } else if (s === step) {
                            stepperEl.classList.add('active');
                        }
                    }
                });

                currentStep = step;
            }

            // Simple validation function
            function validateStep(step) {
                const stepDiv = document.getElementById('step' + step);
                if (!stepDiv) return true;

                // Check standard HTML5 validation for required fields
                const inputs = stepDiv.querySelectorAll('input[required], select[required], textarea[required]');
                for (const input of inputs) {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        return false;
                    }
                }
                return true;
            }

            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    // Check validation before proceeding
                    if (validateStep(currentStep)) {
                        if (currentStep < 3) showStep(currentStep + 1);
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep > 1) showStep(currentStep - 1);
                });
            });

            window.toggleCertUpload = function () {
                const hasCertElement = document.getElementById('hasCert');
                const uploadDiv = document.getElementById('certUpload');
                if (hasCertElement && uploadDiv) {
                    uploadDiv.style.display = (hasCertElement.value == '1') ? 'block' : 'none';
                }
            };
        })();
    </script>
@endsection
