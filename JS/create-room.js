document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector('form');
  const insertButton = document.querySelector('button[type="submit"]');

  form.addEventListener('submit', function (event) {
      event.preventDefault(); // Prevent form submission

      let isValid = true;

      // Validate Room Name
      const roomNameInput = document.getElementById('room_name');
      const roomName = roomNameInput.value.trim();
      const roomNameError = document.getElementById('room_name_error');
      if (roomName === '') {
          roomNameError.innerText = 'Room Name is required';
          isValid = false;
      } else {
          roomNameError.innerText = '';
      }

      // Validate Price
      const priceInput = document.getElementById('price');
      const price = priceInput.value.trim();
      const priceError = document.getElementById('price_error');
      if (price === '') {
          priceError.innerText = 'Price is required';
          isValid = false;
      } else {
          priceError.innerText = '';
      }

      // Validate Room Size
      const roomSizeInput = document.getElementById('room_size');
      const roomSize = roomSizeInput.value.trim();
      const roomSizeError = document.getElementById('room_size_error');
      if (roomSize === '') {
          roomSizeError.innerText = 'Room Size is required';
          isValid = false;
      } else {
          roomSizeError.innerText = '';
      }

      // Validate Number of Rooms
      const numRoomsInput = document.getElementById('num_rooms');
      const numRooms = numRoomsInput.value.trim();
      const numRoomsError = document.getElementById('num_rooms_error');
      if (numRooms === '') {
          numRoomsError.innerText = 'Number of Rooms is required';
          isValid = false;
      } else {
          numRoomsError.innerText = '';
      }

      // Validate Number of Bathrooms
      const numBathroomsInput = document.getElementById('num_bathrooms');
      const numBathrooms = numBathroomsInput.value.trim();
      const numBathroomsError = document.getElementById('num_bathrooms_error');
      if (numBathrooms === '') {
          numBathroomsError.innerText = 'Number of Bathrooms is required';
          isValid = false;
      } else {
          numBathroomsError.innerText = '';
      }

      // Validate Image Path
      const imagePathInput = document.getElementById('image_path');
      const imagePath = imagePathInput.value.trim();
      const imagePathError = document.getElementById('image_path_error');
      if (imagePath === '') {
          imagePathError.innerText = 'Image Path is required';
          isValid = false;
      } else {
          imagePathError.innerText = '';
      }

      // If form is valid, submit the form
      if (isValid) {
          // Collect form data
          const formData = new FormData(form);

          // Send form data using AJAX
          fetch('../php/create-room-process.php', {
              method: 'POST',
              body: formData
          })
              .then(response => {
                  if (!response.ok) {
                      throw new Error('Failed to submit form. Please try again later.');
                  }
                  return response.json();
              })
              .then(data => {
                  console.log('Form submitted successfully:', data);
                  // Optionally, display success message or redirect user
                  // Show Swal.fire with submission result
                  Swal.fire({
                      title: 'Room saved!',
                      text: data.message,
                      icon: 'success',
                      onClose: () => {
                          // Clear the form fields
                          form.reset();
                          window.location.reload();

                      }
                  });
              })
              .catch(error => {
                  console.error('An error occurred while submitting the form:', error);
                  // Optionally, display error message to user
                  Swal.fire({
                      title: 'Error!',
                      text: 'An error occurred while submitting the form. Please try again later.',
                      icon: 'error'
                  });
              })
              .finally(() => {
                  // Re-enable the insert button
                  insertButton.disabled = false;
              });
      }
  });
});


function changeRecordsPerPage(value) {
    var url = new URL(window.location.href);
    url.searchParams.set("records_per_page", value);
    window.location.href = url.toString();
}
document.querySelectorAll('.update-btn').forEach(button => {
    button.addEventListener('click', function() {
        const roomId = this.getAttribute('data-room-id');
        fetch(`../php/fetch-room.php?room_id=${roomId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('update_room_id').value = data.room_id;
                document.getElementById('room_name').value = data.room_name;
                document.getElementById('price').value = data.price;
                document.getElementById('room_size').value = data.room_size;
                document.getElementById('num_rooms').value = data.num_rooms;
                document.getElementById('num_bathrooms').value = data.num_bathrooms;

                // Checkbox handling for amenities
                const amenities = data.amenities.split(', ');
                amenities.forEach(amenity => {
                    document.getElementById(amenity.toLowerCase()).checked = true;
                });
            })
            .catch(error => console.error('Error fetching room details:', error));
    });
});

document.getElementById('updateRoomBtn').addEventListener('click', function() {
    document.getElementById('updateRoomForm').submit();
});
