@extends('frontend.layouts.app')

@section('title', 'Contact Us - Nandhini Silks')

@section('content')
    <main class="contact-main">
        <!-- Hero Section -->
        <section class="contact-hero">
            <div class="contact-hero-overlay"></div>
            <div class="contact-hero-content">
                <h1 class="contact-hero-title">Get In Touch</h1>
                <p class="contact-hero-subtitle">We would love to hear from you and craft your perfect silk experience</p>
            </div>
        </section>

        <!-- Content Section -->
        <section class="contact-content page-shell">
            <div class="contact-grid">
                <!-- Info Side -->
                <div class="contact-info">
                    <h2 class="contact-heading">Visit Our Boutique</h2>
                    <p class="contact-desc">Experience the luxurious touch of authentic handwoven silk sarees in person. Our
                        doors are always open for you.</p>

                    <div class="contact-info-list">
                        <div class="info-item">
                            <div class="info-icon"><img src="{{ asset('images/Vector4.svg') }}" alt="Location"
                                    style="filter: brightness(0) invert(1);"></div>
                            <div class="info-text">
                                <h3>Store Address</h3>
                                <p>Nandhini Silks<br>416/9 Aranmanai Street, S.V. Nagaram<br>Arni - 632317, Thiruvannamalai
                                    dist</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><img src="{{ asset('images/telephone.svg') }}" alt="Phone"
                                    style="filter: brightness(0) invert(1);"></div>
                            <div class="info-text">
                                <h3>Contact Number</h3>
                                <p>+91 96295 52822<br>+91 93631 52822</p>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-icon"><img src="{{ asset('images/email.svg') }}" alt="Email"
                                    style="filter: brightness(0) invert(1);"></div>
                            <div class="info-text">
                                <h3>Email Address</h3>
                                <p>noreply@nandhinisilks.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Side -->
                <div class="contact-form-container">
                    <h2 class="contact-heading">Send a Message</h2>
                    <form class="contact-form validate-form" method="POST" action="{{ route('contact.submit') }}">
                        @csrf
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        </div>
                        <div class="form-group">
                            <label for="message">Your Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="How can we help you today?" required></textarea>
                        </div>
                        <button type="submit" class="contact-submit">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="contact-map-container"
                style="margin-top: 60px; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid rgba(148, 4, 55, 0.05); display: flex;">
                <iframe width="100%" height="450" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"
                    src="https://maps.google.com/maps?q=Nandhini%20Silks,%20416/9%20Aranmanai%20Street,%20S.V.%20Nagaram,%20Arni%20-%20632317,%20Thiruvannamalai%20dist&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    style="filter: grayscale(0.2) contrast(1.1);"></iframe>
            </div>
        </section>
    </main>

@push('scripts')
<script>
$(document).ready(function() {
    // Prevent non-alphabets from being typed
    $('#name').on('input', function() {
        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    });

    // Add custom validation method for letters only
    $.validator.addMethod("lettersOnly", function(value, element) {
        return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
    }, "Please enter only alphabets.");

    // Update validation rules for contact form
    $('.contact-form').validate({
        rules: {
            name: {
                required: true,
                lettersOnly: true
            },
            email: {
                required: true,
                email: true
            },
            message: {
                required: true
            }
        },
        errorElement: 'span',
        errorClass: 'error-text',
        highlight: function(element) {
            $(element).addClass('error-border');
        },
        unhighlight: function(element) {
            $(element).removeClass('error-border');
        }
    });

    $('.contact-form').on('submit', function(e) {
        if (!$(this).valid()) return false;
        e.preventDefault();
        
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');
        const originalBtnText = $btn.text();
        
        $btn.prop('disabled', true).text('Sending...');
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonColor: '#940437'
                    });
                    $form[0].reset();
                } else {
                    // Show error from server if success is false but didn't throw 422
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonColor: '#940437'
                    });
                }
            },
            error: function(xhr) {
                // Remove popups as requested, errors are shown by jQuery Validate or we can use the response
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    // This will highlight the fields manually if needed, 
                    // though jQuery Validate often handles this if the server response keys match
                    $form.validate().showErrors(errors);
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred. Please try again later.',
                        icon: 'error',
                        confirmButtonColor: '#940437'
                    });
                }
            },
            complete: function() {
                $btn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
});
</script>
@endpush
@endsection
