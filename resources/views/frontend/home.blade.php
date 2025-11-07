@extends('layouts.frontend')

@section('title', 'Stone Cobblers Central')

@section('content')
<div class="landing" id="home">
    <div class="landing__sparkles" aria-hidden="true"></div>

    <header class="landing__bar">
        <div class="landing__brand">
            <span class="landing__logo">SC</span>
            <div class="landing__brand-text">
                <strong>Stone Cobblers</strong>
                <span>Central</span>
            </div>
        </div>
        <nav class="landing__nav">
            <a href="#about">About</a>
            <a href="#work">Services</a>
            <a href="#contact">Contact</a>
        </nav>
        <a href="{{ route('login') }}" class="btn secondary landing__login">Client Login</a>
    </header>

    <main class="landing__main">
        <section class="landing-hero">
            <div class="landing-hero__copy">
                <h1>Stone surfaces crafted to shine every day.</h1>
                <p>We design, fabricate, and install countertops that combine timeless materials with modern precision. Let’s refresh your kitchen, bath, or commercial space with finishes that stay brilliant.</p>
                <div class="landing-hero__actions">
                    <a class="btn theme" href="#work">See our work</a>
                    <a class="btn secondary" href="#contact">Request a quote</a>
                </div>
            </div>
            <div class="landing-hero__visual" aria-hidden="true">
                <div class="landing-hero__card">
                    <div class="landing-hero__glow"></div>
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Stone Cobblers fabrication shop">
                </div>
            </div>
        </section>

        <section id="about" class="landing-section">
            <h2 class="landing-section__title">Why teams trust Stone Cobblers</h2>
            <div class="landing-grid">
                <article class="landing-card">
                    <h3>Experienced fabricators</h3>
                    <p>Our crew brings decades of hands-on countertop expertise to every project, with quality checks at each step.</p>
                </article>
                <article class="landing-card">
                    <h3>Thoughtful collaboration</h3>
                    <p>Designers, contractors, and homeowners count on our responsive communication and prompt scheduling.</p>
                </article>
                <article class="landing-card">
                    <h3>Turnkey delivery</h3>
                    <p>From stone selection to installation day, we handle the details so your surfaces arrive flawless and on time.</p>
                </article>
            </div>
        </section>

        <section id="work" class="landing-section landing-section--muted">
            <h2 class="landing-section__title">Services at a glance</h2>
            <ul class="landing-list">
                <li><span>✔</span> Kitchens, baths, and outdoor living surfaces</li>
                <li><span>✔</span> Template, fabrication, and installation</li>
                <li><span>✔</span> Commercial fit-outs and multi-unit projects</li>
                <li><span>✔</span> Stone care, repair, and refinishing</li>
            </ul>
        </section>

        <section id="contact" class="landing-section landing-section--cta">
            <h2 class="landing-section__title">Ready to start your next project?</h2>
            <p>Share your drawings or schedule a visit to our shop—our team will prepare a tailored quote.</p>
            <div class="landing-contact">
                <a class="landing-contact__link" href="tel:{{ preg_replace('/[^0-9+]/', '', setting('company_phone', '(978) 555-0123')) }}">{{ setting('company_phone', '(978) 555-0123') }}</a>
                <a class="landing-contact__link" href="mailto:{{ setting('company_email', 'info@stonecobblers.com') }}">{{ setting('company_email', 'info@stonecobblers.com') }}</a>
            </div>
        </section>
    </main>

    <footer class="landing__footer">
        <span>© {{ now()->year }} Stone Cobblers. All rights reserved.</span>
    </footer>
</div>
@endsection
