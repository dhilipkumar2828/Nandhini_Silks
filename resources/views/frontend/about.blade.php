@extends('frontend.layouts.app')

@section('title', 'About Us - Nandhini Silks')

@section('content')
    <main class="about-main">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="about-hero-overlay"></div>
            <div class="about-hero-content">
                <h1 class="about-hero-title">Our Heritage</h1>
                <p class="about-hero-subtitle">The legacy of pure silks woven with tradition and passion</p>
            </div>
        </section>

        <!-- Story Section -->
        <section class="about-story">
            <div class="page-shell about-story-inner">
                <div class="about-story-media">
                    <img src="{{ asset('images/Rectangle 31.png') }}" alt="Weavers at work" />
                    <div class="about-story-badge">Est. 1990</div>
                </div>
                <div class="about-story-text">
                    <h2 class="about-section-heading">A Thread of Passion</h2>
                    <p>At Nandhini Silks, every thread tells a unique story of artistry and craftsmanship. Born from a
                        profound appreciation for authentic Indian weaves, we have spent decades curating and creating the
                        finest collection of silk sarees that reflect the true essence of our culturally rich heritage.</p>
                    <p>Our journey began in the vibrant weaving clusters of Tamil Nadu, and has since grown into a beloved
                        destination for those seeking elegance, quality, and timeless grace. Combining traditional weaving
                        techniques with modern aesthetics, we ensure that every piece is a masterpiece.</p>
                </div>
            </div>
        </section>

        <!-- Mission & Vision Section -->
        <section class="about-values">
            <div class="page-shell">
                <div class="mission-vision-grid">
                    <div class="mission-vision-card">
                        <div class="value-icon"><img src="{{ asset('images/mission.svg') }}" alt="Mission" /></div>
                        <h3>Our Mission</h3>
                        <p>Crafted Textures, Curated for Every Occasion. We strive to bring you the finest handwoven silks
                            that celebrate every milestone with elegance and grace.</p>
                    </div>
                    <div class="mission-vision-card">
                        <div class="value-icon"><img src="{{ asset('images/vision.svg') }}" alt="Vision" /></div>
                        <h3>Our Vision</h3>
                        <p>Fresh weaves, added daily. Our vision is to ensure that the legacy of handwoven sarees is
                            discovered anew by every generation, tailored just for you.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Premium Banner -->
        <section class="about-legacy">
            <div class="about-legacy-card">
                <h2>Step Into Elegance</h2>
                <p>Discover the finest collection of premium silk sarees designed exclusively for your grandest occasions.
                    Let us be a part of your celebrations.</p>
                <a href="{{ route('shop') }}" class="legacy-btn">View Collections</a>
            </div>
        </section>
    </main>
@endsection
