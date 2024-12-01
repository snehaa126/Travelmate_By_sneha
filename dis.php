
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disclaimer - Travel Partner Finder</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1>Disclaimer</h1>
        </header>
        <main>
            <p class="intro">Please read this disclaimer carefully before using our Travel Partner Finder service:</p>
            <ul class="disclaimer-list">
                <li>The website connects users to potential travel partners.</li>
                <li>We do not guarantee compatibility or safety of travel partners.</li>
                <li>Users are responsible for their own safety and decision-making.</li>
                <li>We are not liable for any disputes, issues, or incidents during travel.</li>
                <li>Users must verify personal information before meeting anyone.</li>
                <li>We do not endorse or vet users of the platform.</li>
                <li>Use the platform at your own risk and discretion.</li>
                <li>Terms and conditions apply to all services provided.</li>
            </ul>
            <p class="conclusion">By using our service, you acknowledge that you have read, understood, and agree to this disclaimer.</p>
        </main>
        <footer>
            <a href="home.php" class="btn">Return to Home</a>
        </footer>
    </div>
</body>
</html>

<style>
body {
    font-family: 'Poppins', sans-serif;
    line-height: 1.6;
    color: #333;
    background-color: #f4f4f4;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

header {
    text-align: center;
    margin-bottom: 30px;
}

h1 {
    color: #faa8a8;
    font-size: 2.5em;
}

.intro, .conclusion {
    font-weight: 500;
    margin-bottom: 20px;
}

.disclaimer-list {
    list-style-type: none;
    padding: 0;
}

.disclaimer-list li {
    margin-bottom: 15px;
    padding-left: 30px;
    position: relative;
}

.disclaimer-list li::before {
    content: "➡️";
    position: absolute;
    left: 0;
    color: #3498db;
}

footer {
    text-align: center;
    margin-top: 30px;
}

.btn {
    display: inline-block;
    background-color: #faa8a8;
    color: #fff;
    padding: 10px 20px;
    text-decoration: none;
    font-weight:bold;
    border-radius: 5px;
}

</style>