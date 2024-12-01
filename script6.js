// Sample user data (replace this with actual data from your backend)
const users = [
  {
      id: 1,
      name: "Alice Smith",
      age: 28,
      gender: "Female",
      city: "New York",
      interests: ["Hiking", "Photography", "Food"],
      languages: ["English", "Spanish"],
      image: "https://randomuser.me/api/portraits/women/1.jpg"
  },
  {
      id: 2,
      name: "Bob Johnson",
      age: 32,
      gender: "Male",
      city: "London",
      interests: ["Museums", "Art", "History"],
      languages: ["English", "French"],
      image: "https://randomuser.me/api/portraits/men/1.jpg"
  },
  {
      id: 3,
      name: "Carol Williams",
      age: 25,
      gender: "Female",
      city: "Paris",
      interests: ["Fashion", "Cuisine", "Architecture"],
      languages: ["French", "English", "Italian"],
      image: "https://randomuser.me/api/portraits/women/2.jpg"
  }
];

function createProfileCard(user) {
  const card = document.createElement('div');
  card.classList.add('profile-card');
  card.innerHTML = `
      <img src="${user.image}" alt="${user.name}">
      <h3>${user.name}</h3>
      <p>${user.age} years old, ${user.gender}</p>
      <p>City: ${user.city}</p>
      <button onclick="showProfileDetails(${user.id})">View Profile</button>
  `;
  return card;
}

function showProfileDetails(userId) {
  const user = users.find(u => u.id === userId);
  const modal = document.getElementById('profileModal');
  const profileDetails = document.getElementById('profileDetails');
  
  profileDetails.innerHTML = `
      <h2>${user.name}</h2>
      <img src="${user.image}" alt="${user.name}" style="width: 200px; height: 200px; object-fit: cover;">
      <p>Age: ${user.age}</p>
      <p>Gender: ${user.gender}</p>
      <p>City: ${user.city}</p>
      <p>Interests: ${user.interests.join(', ')}</p>
      <p>Languages: ${user.languages.join(', ')}</p>
  `;
  
  modal.style.display = 'block';
}

function searchProfiles() {
  const searchTerm = document.getElementById('citySearch').value.toLowerCase();
  const profileContainer = document.getElementById('profileContainer');
  profileContainer.innerHTML = '';
  
  const filteredUsers = users.filter(user => user.city.toLowerCase().includes(searchTerm));
  
  filteredUsers.forEach(user => {
      const card = createProfileCard(user);
      profileContainer.appendChild(card);
  });
}

// Close the modal when clicking on the close button or outside the modal
window.onclick = function(event) {
  const modal = document.getElementById('profileModal');
  if (event.target == modal || event.target.classList.contains('close')) {
      modal.style.display = 'none';
  }
}

// Initial load of all profiles
window.onload = function() {
  const profileContainer = document.getElementById('profileContainer');
  users.forEach(user => {
      const card = createProfileCard(user);
      profileContainer.appendChild(card);
  });
}