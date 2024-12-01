<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Travel Mate</title>
    <link rel="stylesheet" href="conts.css">
</head>
<style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #fffbfb;
    color: #000;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

.contact-container {
    background-color: #faa8a8;
    border: 2px solid #000;
    border-radius: 10px;
    padding: 30px;
    width: 50%;
    max-width: 500px;
    text-align: center;
}

.contact-container h1 {
    color: #000;
    font-weight: 800;
    margin-bottom: 20px;
   
   
}

.contact-container p {
    margin-bottom: 30px;
    font-size: medium;
    color: #000;
    font-weight: 600;
    
}

.contact-form label {
    display: block;
    text-align: left;
    margin-bottom: 8px;
    color: #000;
    font-weight: 600;

}

.contact-form input,
.contact-form textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 20px;
    border: 1px solid #000;
    border-radius: 5px;
    background-color: #fceaea;
    color: #000;
}

.contact-form button {
    background-color: #000;
    color: #faa8a8;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.contact-form button:hover {
    background-color: #333;
}
</style>
<body>
    <div class="contact-container">
        <h1>Contact Us</h1>
        <p>We'd love to hear from you! Please fill out the form below and we'll get in touch with you as soon as possible.</p>
        
        <form action="process_contact.php" method="post" class="contact-form">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" required>

            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit">Send Message</button>
        </form>
    </div>
</body>
</html>
