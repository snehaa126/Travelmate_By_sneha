<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Travel Mate</title>
    <link rel="stylesheet" href="ab.css">
</head>
<style>body {
    font-family: 'Poppins', sans-serif;
    background-color: #f6f1f1;
    color: #090000;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    overflow-x: hidden;
}

.about-container {
    background-color: #faa8a8;
    padding: 50px;
    border: 2px solid #000;
    border-radius: 10px;
    max-width: 800px;
    text-align: left;
    position: relative;
    overflow: hidden;
}

.about-content h1 {
    font-size: 36px;
    margin-bottom: 20px;
    position: relative;

}

.about-content h2 {
    font-size: 28px;
    margin-top: 30px;
    position: relative;
    animation: fade-in 1.5s ease-out;
}

.about-content p {
    font-size: 18px;
    line-height: 1.6;
    position: relative;
    animation: fade-in 1s ease-out;
}


/* Fade-in animation for subheadings and paragraphs */
@keyframes fade-in {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.about-container::before {
    content: "";
    position: absolute;
    top: -20px;
    left: -20px;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(250, 168, 168, 0.1), rgba(0, 0, 0, 0));
    animation: rotate 10s infinite linear;
    z-index: -1;
    transform-origin: center;
}


</style>
<body>
    <div class="about-container">
        <div class="about-content">
            <h1>About Us</h1>
            <p>Welcome to Travel Mate, your premier platform for finding and connecting with like-minded travel companions. We're passionate about making travel more enjoyable, social, and memorable.</p>

            <h2>Our Story</h2>
            <p>Travel Mate was born out of a simple idea: to make solo travel more exciting and less intimidating. We believe that exploring new destinations with someone who shares your interests and enthusiasm can elevate the travel experience. Our founders, avid travelers themselves, saw an opportunity to create a platform that brings together solo travelers with compatible partners.</p>

            <h2>Our Mission</h2>
            <p>At Travel Mate, our mission is to provide a safe, secure, and user-friendly platform for solo travelers to find and connect with compatible partners. We strive to make travel more accessible, enjoyable, and affordable for everyone.</p>

            <h2>How We Work</h2>
            <p>Our platform allows users to create profiles, showcasing their travel interests, preferences, and goals. Using our proprietary matching algorithm, we connect users with compatible partners who share similar interests and travel styles. Our secure chat feature enables users to get to know each other, plan their trips, and make unforgettable memories together.</p>

            <h2>Join Our Community</h2>
            <p>At Travel Mate, we're dedicated to making travel more social, enjoyable, and accessible. If you're a solo traveler looking for a partner in adventure, join our community today. Create your profile, connect with like-minded travelers, and start planning your dream trips together.</p>
        </div>
    </div>
</body>
</html>
