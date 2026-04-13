<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/style.css">
    <title>Total-Sightings • Tick Visualiser</title>
    <script src="../script/script.js"></script>
</head>

<body class="dashboard-body" onload="loadNavbar()">
    <div id="navbar-container"></div>

    <!-- banner -->
    <section class="banner-section">
        <div class="content-container">
            <h1>Manage User</h1>
            <p> Manage all registered accounts</p>
            <form class="search-form">
                <input type="search" placeholder="Search by ID, Name or Email..." onkeyup="searchBrowswData(this)">
                <button type="submit" class="btn-primary">Search</button>
            </form>
        </div>
    </section>
    <main class="dashboard-container" role="main">
        <div class="dashboard-card">
            <div class="card-header">
                <h2>Browse Users</h2> <input type="button" class="btn-primary" name="Create" value="Create User">
            </div>
            <div class="manage-table-wrapper">
                <table class="manage-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Location</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Anini Srinivasan</td>
                            <td>anini@example.com</td>
                            <td>Sheffield</td>
                            <td>Admin</td>
                            <td>
                                <form>
                                    <input type="submit" class="approve-button-in-list" name="Edit" value="Edit">
                                    <input type="button" class="reject-button-in-list reject-btn" name="delete"
                                        value="Delete">
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Rachel Kirtland</td>
                            <td>rachel@example.com</td>
                            <td>Sheffield</td>
                            <td>User</td>
                            <td>
                                <form>
                                    <input type="submit" class="approve-button-in-list" name="Edit" value="Edit">
                                    <input type="button" class="reject-button-in-list reject-btn" name="delete"
                                        value="Delete" onclick="confirmDelete(this)">
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>