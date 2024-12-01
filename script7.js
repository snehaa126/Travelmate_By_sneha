
// This code runs when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Get the "Add Trip Plan" button
    const addButton = document.querySelector('.add-trip-plan');
    
    // Add a click event listener to the button
    addButton.addEventListener('click', function(e) {
        // Prevent the default form submission
        e.preventDefault();

        // Get the values from the input fields
        const startDate = document.getElementById('trip-date-start').value;
        const endDate = document.getElementById('trip-date-end').value;
        const city = document.getElementById('trip-city').value;
        const budget = document.getElementById('trip-budget').value;

        // Function to format the date as "12 June 2024"
        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate();
            const month = date.toLocaleString('default', { month: 'long' });
            const year = date.getFullYear();
            return `${day} ${month} ${year}`;
        }

        // Create the trip plan string
        const tripPlan = `${formatDate(startDate)} to ${formatDate(endDate)} - ${city} (Budget: $${budget})`;

        // Display the trip plan on the page
        const displayDiv = document.getElementById('trip-plan-display');
        const tripPlanElement = document.createElement('p');
        tripPlanElement.textContent = tripPlan;
        displayDiv.appendChild(tripPlanElement);

        // Clear the input fields
        document.getElementById('trip-date-start').value = '';
        document.getElementById('trip-date-end').value = '';
        document.getElementById('trip-city').value = '';
        document.getElementById('trip-budget').value = '';
    });
});