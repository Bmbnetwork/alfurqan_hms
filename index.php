<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['role'])) {
    switch($_SESSION['role']) {
        case 'admin': header("Location: admin/dashboard.php"); exit();
        case 'doctor': header("Location: doctor/dashboard.php"); exit();
        case 'nurse': header("Location: nurse/dashboard.php"); exit();
        case 'pharmacist': header("Location: pharmacist/dashboard.php"); exit();
        case 'lab_technician': header("Location: laboratory/dashboard.php"); exit();
    }
}

if (isset($_SESSION['patient_id'])) {
    header("Location: patient/dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/logo.png" type="image/png">
    <title>Alfurqan Clinic & Maternity Limited - Home</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; }
        
        /* Navigation */
        .navbar { 
            background: white; 
            padding: 15px 50px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        .logo { display: flex; align-items: center; gap: 15px; }
        .logo img { width: 60px; height: 60px; object-fit: contain; }
        .logo h1 { color: #003366; font-size: 22px; font-weight: 700; }
        .logo p { color: #666; font-size: 12px; }
        .nav-links { display: flex; gap: 30px; align-items: center; }
        .nav-links a { color: #333; text-decoration: none; font-weight: 500; transition: 0.3s; }
        .nav-links a:hover { color: #003366; }
        .nav-btn { 
            padding: 10px 25px; 
            border-radius: 25px; 
            text-decoration: none; 
            font-weight: 600; 
            transition: 0.3s;
        }
        .btn-login { background: #003366; color: white; }
        .btn-login:hover { background: #002244; }
        .btn-register { background: #28a745; color: white; }
        .btn-register:hover { background: #218838; }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, #003366 0%, #0066cc 100%);
            color: white;
            padding: 100px 50px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/logo.png') center/200px no-repeat;
            opacity: 0.1;
        }
        .hero-content { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }
        .hero h1 { font-size: 48px; margin-bottom: 20px; font-weight: 700; }
        .hero p { font-size: 20px; margin-bottom: 40px; opacity: 0.95; }
        .hero-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
        .hero-btn {
            padding: 15px 40px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: 0.3s;
            display: inline-block;
        }
        .hero-btn-primary { background: #28a745; color: white; }
        .hero-btn-primary:hover { background: #218838; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(40, 167, 69, 0.4); }
        .hero-btn-secondary { background: white; color: #003366; }
        .hero-btn-secondary:hover { background: #f0f0f0; transform: translateY(-3px); box-shadow: 0 10px 25px rgba(255,255,255,0.3); }
        
        /* About Section */
        .about {
            padding: 80px 50px;
            background: #f8f9fa;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .section-title {
            text-align: center;
            font-size: 36px;
            color: #003366;
            margin-bottom: 20px;
        }
        .section-subtitle {
            text-align: center;
            color: #666;
            font-size: 18px;
            margin-bottom: 50px;
        }
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }
        .about-text h3 { color: #003366; font-size: 28px; margin-bottom: 20px; }
        .about-text p { color: #555; margin-bottom: 15px; font-size: 16px; }
        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        .stat-box {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .stat-box h4 { color: #003366; font-size: 32px; margin-bottom: 5px; }
        .stat-box p { color: #666; font-size: 14px; }
        
        /* Services Section */
        .services {
            padding: 80px 50px;
            background: white;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }
        .service-card {
            background: #f8f9fa;
            padding: 35px 30px;
            border-radius: 12px;
            text-align: center;
            transition: 0.3s;
            border-top: 4px solid #003366;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }
        .service-icon {
            font-size: 50px;
            margin-bottom: 20px;
        }
        .service-card h3 {
            color: #003366;
            margin-bottom: 15px;
            font-size: 22px;
        }
        .service-card p {
            color: #666;
            font-size: 15px;
        }
        
        /* Why Choose Us */
        .why-us {
            padding: 80px 50px;
            background: linear-gradient(135deg, #003366 0%, #0066cc 100%);
            color: white;
        }
        .why-us .section-title { color: white; }
        .why-us .section-subtitle { color: rgba(255,255,255,0.9); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .feature-item {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .feature-item h4 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .feature-item p {
            opacity: 0.9;
            font-size: 15px;
        }
        
        /* CTA Section */
        .cta {
            padding: 80px 50px;
            background: #f8f9fa;
            text-align: center;
        }
        .cta h2 {
            color: #003366;
            font-size: 36px;
            margin-bottom: 20px;
        }
        .cta p {
            color: #666;
            font-size: 18px;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Contact Section */
        .contact {
            padding: 80px 50px;
            background: white;
        }
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }
        .contact-card {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }
        .contact-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }
        .contact-card h4 {
            color: #003366;
            margin-bottom: 10px;
        }
        .contact-card p {
            color: #666;
        }
        
        /* Footer */
        .footer {
            background: #002244;
            color: white;
            padding: 40px 50px 20px;
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 40px;
            margin-bottom: 30px;
        }
        .footer-section h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }
        .footer-section p, .footer-section a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: block;
            margin-bottom: 8px;
            transition: 0.3s;
        }
        .footer-section a:hover {
            color: white;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
            font-size: 14px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .navbar { padding: 15px 20px; flex-direction: column; gap: 15px; }
            .nav-links { flex-wrap: wrap; justify-content: center; }
            .hero { padding: 60px 20px; }
            .hero h1 { font-size: 32px; }
            .hero p { font-size: 16px; }
            .about, .services, .why-us, .cta, .contact { padding: 60px 20px; }
            .about-grid { grid-template-columns: 1fr; }
            .footer-content { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- Navigation -->
<nav class="navbar">
    <div class="logo">
        <img src="assets/logo.png" alt="Alfurqan Clinic">
        <div>
            <h1>Alfurqan Clinic</h1>
            <p>& Maternity Limited</p>
        </div>
    </div>
    <div class="nav-links">
        <a href="#about">About</a>
        <a href="#services">Services</a>
        <a href="#contact">Contact</a>
        <a href="patient/register.php" class="nav-btn btn-register">Register</a>
        <a href="login.php" class="nav-btn btn-login">Login</a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Welcome to Alfurqan Clinic & Maternity Limited</h1>
        <p>Your trusted healthcare partner in Bauchi, providing quality medical services with compassion, excellence, and modern technology.</p>
        <div class="hero-buttons">
            <a href="patient/register.php" class="hero-btn hero-btn-primary">📝 Register as Patient</a>
            <a href="login.php" class="hero-btn hero-btn-secondary">🔐 Login to Portal</a>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about" id="about">
    <div class="container">
        <h2 class="section-title">About Our Clinic</h2>
        <p class="section-subtitle">Dedicated to providing exceptional healthcare services to our community</p>
        
        <div class="about-grid">
            <div class="about-text">
                <h3>Excellence in Healthcare Since 2020</h3>
                <p>Alfurqan Clinic & Maternity Limited is a premier healthcare facility located in Bauchi, Nigeria. We are committed to delivering comprehensive medical services that cater to the diverse needs of our patients.</p>
                <p>Our state-of-the-art facility is equipped with modern medical technology and staffed by a team of highly qualified healthcare professionals who are dedicated to providing personalized care.</p>
                <p>From general consultations to specialized maternity care, laboratory services, and pharmacy, we offer a complete range of healthcare solutions under one roof.</p>
                
                <div class="about-stats">
                    <div class="stat-box">
                        <h4>5000+</h4>
                        <p>Happy Patients</p>
                    </div>
                    <div class="stat-box">
                        <h4>15+</h4>
                        <p>Expert Doctors</p>
                    </div>
                    <div class="stat-box">
                        <h4>24/7</h4>
                        <p>Emergency Care</p>
                    </div>
                    <div class="stat-box">
                        <h4>10+</h4>
                        <p>Years Experience</p>
                    </div>
                </div>
            </div>
            <div class="about-image">
                <div style="background: linear-gradient(135deg, #003366 0%, #0066cc 100%); height: 500px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 120px;">
                    🏥
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services" id="services">
    <div class="container">
        <h2 class="section-title">Our Services</h2>
        <p class="section-subtitle">Comprehensive healthcare solutions tailored to your needs</p>
        
        <div class="services-grid">
            <div class="service-card">
                <div class="service-icon">🩺</div>
                <h3>General Consultation</h3>
                <p>Expert medical consultations with experienced doctors for all your health concerns and preventive care.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🤰</div>
                <h3>Maternity Care</h3>
                <p>Complete antenatal and postnatal care with modern facilities for safe motherhood and healthy babies.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">🔬</div>
                <h3>Laboratory Services</h3>
                <p>Advanced diagnostic testing with accurate results delivered by certified laboratory technicians.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">💊</div>
                <h3>Pharmacy</h3>
                <p>Well-stocked pharmacy with genuine medications and professional pharmaceutical counseling.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon"></div>
                <h3>Emergency Care</h3>
                <p>24/7 emergency medical services with rapid response and critical care capabilities.</p>
            </div>
            
            <div class="service-card">
                <div class="service-icon">📅</div>
                <h3>Online Appointments</h3>
                <p>Book appointments online and access your medical records through our patient portal.</p>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="why-us">
    <div class="container">
        <h2 class="section-title">Why Choose Alfurqan Clinic?</h2>
        <p class="section-subtitle">We stand out for our commitment to quality and patient satisfaction</p>
        
        <div class="features-grid">
            <div class="feature-item">
                <h4>✓ Qualified Professionals</h4>
                <p>Our team consists of highly trained doctors, nurses, and specialists with years of experience.</p>
            </div>
            
            <div class="feature-item">
                <h4>✓ Modern Facilities</h4>
                <p>State-of-the-art medical equipment and comfortable patient care environments.</p>
            </div>
            
            <div class="feature-item">
                <h4>✓ Affordable Care</h4>
                <p>Quality healthcare services at competitive prices with transparent billing.</p>
            </div>
            
            <div class="feature-item">
                <h4>✓ Patient-Centered</h4>
                <p>Personalized treatment plans and compassionate care for every patient.</p>
            </div>
            
            <div class="feature-item">
                <h4>✓ Digital Records</h4>
                <p>Secure electronic health records for easy access and continuity of care.</p>
            </div>
            
            <div class="feature-item">
                <h4>✓ Convenient Access</h4>
                <p>Online booking, patient portal, and flexible appointment scheduling.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <h2>Ready to Experience Quality Healthcare?</h2>
        <p>Join thousands of satisfied patients who trust Alfurqan Clinic for their healthcare needs. Register today and take the first step towards better health.</p>
        <div class="cta-buttons">
            <a href="patient/register.php" class="hero-btn hero-btn-primary">📝 Register Now</a>
            <a href="login.php" class="hero-btn hero-btn-secondary">🔐 Login to Portal</a>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact" id="contact">
    <div class="container">
        <h2 class="section-title">Contact Us</h2>
        <p class="section-subtitle">Get in touch with us for appointments and inquiries</p>
        
        <div class="contact-grid">
            <div class="contact-card">
                <div class="contact-icon">📍</div>
                <h4>Location</h4>
                <p>Bauchi, Nigeria</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-icon">📞</div>
                <h4>Phone</h4>
                <p>0913-781-4650</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-icon">✉️</div>
                <h4>Email</h4>
                <p>info@alfurqanclinic.com</p>
            </div>
            
            <div class="contact-card">
                <div class="contact-icon">🕐</div>
                <h4>Working Hours</h4>
                <p>24/7 Emergency<br>Mon-Sat: 8AM - 8PM</p>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Alfurqan Clinic & Maternity Limited</h3>
            <p>Providing quality healthcare services to the community with compassion, excellence, and modern technology.</p>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="#about">About Us</a>
            <a href="#services">Services</a>
            <a href="patient/register.php">Register</a>
            <a href="login.php">Login</a>
        </div>
        
        <div class="footer-section">
            <h3>Contact Info</h3>
            <p>📍 Bauchi, Nigeria</p>
            <p>📞 0913-781-4650</p>
            <p>✉️ info@alfurqanclinic.com</p>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Alfurqan Clinic & Maternity Limited. All Rights Reserved.</p>
    </div>
</footer>

</body>
</html>