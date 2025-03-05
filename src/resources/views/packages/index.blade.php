<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h1>Available Travel Packages</h1>

    <table border="1">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Available Seats</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="package-list">
            <!-- Packages will be loaded here via AJAX -->
        </tbody>
    </table>

    <script>
        $(document).ready(function () {
            const token = localStorage.getItem('token'); // Retrieve stored token

            function loadPackages() {
                axios.get('/api/packages', {
                    headers: { 'Authorization': `Bearer ${token}` }
                })
                .then(response => {
                    let packages = response.data;
                    let packageList = '';

                    packages.forEach(pkg => {
                        packageList += `
                            <tr>
                                <td>${pkg.name}</td>
                                <td>${pkg.description}</td>
                                <td>${pkg.price}</td>
                                <td>${pkg.available_seats}</td>
                                <td>
                                    ${pkg.available_seats > 0 
                                        ? `<button onclick="bookPackage(${pkg.id})">Book Now</button>` 
                                        : `<button disabled>Fully Booked</button>`}
                                </td>
                            </tr>`;
                    });

                    $('#package-list').html(packageList);
                })
                .catch(error => console.log(error));
            }

            window.bookPackage = function (packageId) {
                axios.post('/api/book', { package_id: packageId }, {
                    headers: { 'Authorization': `Bearer ${token}` }
                })
                .then(response => {
                    alert(response.data.message);
                    loadPackages(); // Refresh package list after booking
                })
                .catch(error => {
                    alert(error.response.data.error);
                });
            }

            loadPackages(); // Load packages on page load
        });
    </script>

</body>
</html>
