<?php
// Include your header and any necessary files here
include('db.php');
?>

<!-- About Us Page Content -->
<div class="about-us-container">
    <!-- Slideshow Section -->
    <section class="slideshow">
        <div class="slides">
            <img src="image/image2.jpg" alt="LocalHands - Connecting Workers and Users">
            <img src="image/image5.jpeg" alt="Find Skilled Workers Near You">
            <img src="image/image6.webp" alt="Discover Local Experts">
        </div>
    </section>

    <!-- Main Content -->
    <section class="about-us-header">
        <h1>About LocalHand</h1>
        <link rel="stylesheet" href="style.css">
        <p>LocalHand is a web-based service platform designed to connect users with skilled workers in their local area. The platform offers a seamless way for workers to register and showcase their skills, while users can easily search, hire, and review service providers based on location, specialty, and ratings. LocalHands aims to simplify the process of finding reliable service professionals while providing workers with better visibility and job opportunities.</p>
    </section>

    <!-- Project Objectives -->
    <section class="project-objectives">
        <h2>Project Objectives</h2>
        <ul>
            <li>Facilitate efficient connections between users and workers.</li>
            <li>Ensure secure and convenient user-worker interactions.</li>
            <li>Provide tools for workers to manage their profiles and availability.</li>
            <li>Empower users with comprehensive search and filtering options.</li>
        </ul>
    </section>

    <!-- Core Features -->
    <section class="core-features">
        <h2>Core Features</h2>
        <p>LocalHand offers the following key features for both users and workers:</p>
        <ul>
            <li><strong>Worker Registration:</strong> Workers can sign up and add details: name, contact number, field of work, location, bio, and hourly rate.</li>
            <li><strong>Search for Workers:</strong> Users can search for workers by location, field of work, rating, or availability.</li>
            <li><strong>Ratings & Reviews:</strong> Users can rate workers (1-5 stars) and leave reviews.</li>
            <li><strong>Location-based Search:</strong> Integration with Google Maps API to display workers near a user's location or sort by area.</li>
            <li><strong>Worker Profiles:</strong> Detailed worker profiles showing their ratings, reviews, and portfolio (optional).</li>
        </ul>
    </section>

    <!-- Worker-Specific Features -->
    <section class="worker-specific-features">
        <h3>Worker-Specific Features</h3>
        <ul>
            <li><strong>Availability Status:</strong> Workers can mark themselves as available/unavailable.</li>
            <li><strong>Portfolio Uploads:</strong> Workers can upload photos of their past work.</li>
            <li><strong>Job History:</strong> Workers can view completed jobs and associated reviews.</li>
        </ul>
    </section>

    <!-- User-Specific Features -->
    <section class="user-specific-features">
        <h3>User-Specific Features</h3>
        <ul>
            <li><strong>Job Requests:</strong> Users can post job requests for workers to respond to.</li>
            <li><strong>Save Favorites:</strong> Users can bookmark favorite workers for future use.</li>
            <li><strong>Contact Workers:</strong> Call option for direct communication with workers.</li>
            <li><strong>Budget Filter:</strong> Search workers by budget range.</li>
        </ul>
    </section>

    <!-- Admin Features -->
    <section class="admin-features">
        <h3>Admin Features</h3>
        <ul>
            <li><strong>Admin Dashboard:</strong> Admin can approve/reject worker registrations and moderate reviews and reported content.</li>
            <li><strong>Analytics:</strong> Insights into user and worker activity, such as popular locations or services.</li>
        </ul>
    </section>

    <!-- Footer -->
    <section class="footer">
        <p>At LocalHand, we are committed to making the process of finding reliable service providers as simple and efficient as possible. We are constantly working to improve the platform and add new features to better serve our users and workers.</p>
    </section>
</div>

<?php
// Include your footer file
include('footer.php');
?>
